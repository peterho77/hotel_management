<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    protected $table = 'room_type';
    public $timestamps = false;

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, "room_type_branch", "room_type_id", "branch_id")
            ->withPivot('branch_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'room_type_id', 'id');
    }
    public function images(): HasMany
    {
        return $this->hasMany(Images::class, 'room_type_id');
    }
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenities::class, 'room_type_amenities', 'room_type_id', 'amenity_id');
    }
    public function room_options(): HasMany
    {
        return $this->hasMany(RoomOption::class, 'room_type_id', 'id');
    }
    protected $fillable =
    [
        'name',
        'bed_type',
        'description',
        'max_adults',
        'max_children',
        'total_quantity',
        'status',
        'base_price',
        'hourly_rate',
        'full_day_rate',
        'overnight_rate',
    ];

    public function refreshQuantity()
    {
        $totalRooms = $this->rooms()->count();
        $totalActiveRooms = $this->rooms()->where('status', 'active')->count();

        // Cập nhật cho chính RoomType (Nếu có thay đổi)
        if ($this->total_quantity !== $totalRooms) {
            $this->total_quantity = $totalRooms;
            $this->saveQuietly(); // Lưu mà không kích hoạt event 'updated' để tránh loop
        }

        $this->room_options()->update([
            'available_quantity' => $totalActiveRooms
        ]);

        return [
            'total_rooms' => $totalRooms,
            'available_rooms' => $totalActiveRooms
        ];
    }

    /**
     * Hàm tiện ích để chạy đồng bộ lại toàn bộ hệ thống (dùng trong Seeder hoặc Tinker)
     */
    public static function syncAllQuantities()
    {
        $types = self::all();
        $count = 0;
        foreach ($types as $type) {
            $type->refreshQuantity();
            $count++;
        }
        return "Đã đồng bộ xong dữ liệu cho {$count} loại phòng.";
    }

    protected static function booted()
    {
        // Sự kiện khi Admin sửa thủ công RoomType (ví dụ đổi status active -> inactive)
        static::updated(function ($roomType) {

            // Nếu Admin đổi trạng thái của Loại phòng (VD: Tắt hoạt động cả loại phòng này)
            if ($roomType->wasChanged('status')) {
                if ($roomType->status !== 'active') {
                    // Nếu loại phòng bị tắt -> Set available của Option về 0 hết
                    $roomType->room_options()->update(['available_quantity' => 0]);
                } else {
                    // Nếu bật lại -> Tính toán lại số lượng active
                    $roomType->refreshQuantity();
                }
            }
        });
    }
}
