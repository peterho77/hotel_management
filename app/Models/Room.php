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
        // Định nghĩa hàm callback chung để đỡ viết lại code
        $triggerRefresh = function ($room) {
            if ($room->roomType) {
                // Gọi hàm đếm lại bên RoomType (Hàm này đã lo cả total lẫn available)
                $room->roomType->refreshQuantity();
            }
        };

        // 1. Khi tạo mới phòng (Created) -> Tính lại
        static::created($triggerRefresh);

        // 2. Khi xóa phòng (Deleted) -> Tính lại
        static::deleted($triggerRefresh);

        // 3. Khi cập nhật thông tin phòng (Updated)
        static::updated(function ($room) {

            // Chỉ chạy khi có thay đổi quan trọng: Đổi Status hoặc Đổi Loại phòng
            if ($room->isDirty('status') || $room->isDirty('room_type_id')) {

                // Cập nhật cho loại phòng hiện tại (New Type)
                if ($room->roomType) {
                    $room->roomType->refreshQuantity();
                }

                // TRƯỜNG HỢP ĐẶC BIỆT: Nếu đổi loại phòng (VD: Standard -> Deluxe)
                // Thì phải cập nhật lại số lượng cho cả loại phòng cũ (Standard) nữa
                if ($room->isDirty('room_type_id')) {
                    $oldTypeId = $room->getOriginal('room_type_id');
                    $oldType = RoomType::find($oldTypeId);

                    if ($oldType) {
                        $oldType->refreshQuantity();
                    }
                }
            }
        });
    }
}
