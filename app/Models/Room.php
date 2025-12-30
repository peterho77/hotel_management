<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Room extends Model
{
    protected $table = "room";
    protected $fillable = ['name', 'area', 'status', 'housekeeping_status', 'note', 'branch_id', 'room_type_id'];
    const HK_CLEAN = 'clean';
    const HK_DIRTY = 'dirty';
    public $timestamps = false;

    public function room_type(): BelongsTo
    {
        return $this->belongsTo(RoomType::class)->withDefault([
            'name' => 'Chưa xếp hạng phòng',
        ]);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withDefault([
            'name' => 'Chưa có chi nhánh',
        ]);
    }
    public function booking_items()
    {
        return $this->hasMany(RoomBookingItem::class, 'assigned_room_id');
    }
    public function current_booking_item()
    {
        return $this->hasOne(RoomBookingItem::class, 'assigned_room_id')
            ->ofMany([
                'id' => 'max', // Lấy item mới nhất nếu có trùng lặp
            ], function ($query) {
                // Filter: Chỉ lấy những item thuộc về Booking đang hoạt động
                $query->whereHas('booking', function ($q) {
                    $q->where('status', 'checked_in')
                        ->whereDate('check_in', '<=', now());
                });
            });
    }
    public function getCurrentGuestNameAttribute()
    {
        return $this->currentBookingItem?->booking?->customer?->name ?? null;
    }
    public function getUiStatusAttribute()
    {
        // Ưu tiên 1: Nếu phòng đang bảo trì -> Màu Xám
        if ($this->status === 'inactive') {
            return 'maintenance';
        }

        // Lấy thông tin đặt phòng hiện tại (nếu có)
        $currentItem = $this->currentBookingItem;

        // Ưu tiên 2: Nếu có khách đang ở
        if ($currentItem && $currentItem->booking) {
            $checkoutTime = Carbon::parse($currentItem->booking->check_out);
            $now = Carbon::now();

            // Check A: Quá giờ trả phòng -> Màu Cam
            if ($now->gt($checkoutTime)) {
                return 'overdue';
            }

            // Check B: Sắp trả phòng (còn dưới 2 tiếng) -> Màu Xanh Cổ Vịt (Teal)
            // diffInHours(date, false) trả về số dương nếu chưa tới, số âm nếu qua rồi
            if ($now->diffInHours($checkoutTime, false) < 2 && $now->diffInHours($checkoutTime, false) >= 0) {
                return 'checkout';
            }

            // Mặc định: Đang ở bình thường -> Màu Xanh Lá
            return 'occupied';
        }

        // Mặc định: Phòng trống -> Màu Trắng
        return 'empty';
    }
    public function getCurrentTimeStrAttribute()
    {
        $item = $this->currentBookingItem;
        if (!$item || !$item->booking) return null;

        $in = Carbon::parse($item->booking->check_in);
        $out = Carbon::parse($item->booking->check_out);
        
        // Format hiển thị tùy ý: VD "01/11 - 03/11"
        return $in->format('d/m') . ' - ' . $out->format('d/m');
    }

    protected static function booted()
    {
        // 1. Khi tạo mới một phòng (Created)
        static::created(function ($room) {
            if ($room->roomType) {
                $room->roomType->increment('total_quantity');
            }
        });

        // 2. Khi xóa một phòng (Deleted)
        static::deleted(function ($room) {
            if ($room->roomType) {
                // Kiểm tra để không bị âm
                if ($room->roomType->total_quantity > 0) {
                    $room->roomType->decrement('total_quantity');
                }
            }
        });

        // Trường hợp đặc biệt: Admin đổi phòng này từ loại A sang loại B
        static::updated(function ($room) {
            // Kiểm tra xem cột 'room_type_id' có bị thay đổi không
            if ($room->isDirty('room_type_id')) {
                // Giảm số lượng ở loại phòng cũ
                $oldRoomTypeId = $room->getOriginal('room_type_id');
                $oldRoomType = RoomType::find($oldRoomTypeId);
                if ($oldRoomType && $oldRoomType->total_quantity > 0) {
                    $oldRoomType->decrement('total_quantity');
                }

                // Tăng số lượng ở loại phòng mới
                $newRoomType = RoomType::find($room->room_type_id);
                if ($newRoomType) {
                    $newRoomType->increment('total_quantity');
                }
            }
        });
    }
}
