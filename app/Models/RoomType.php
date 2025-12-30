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
        // Đếm số phòng thực tế trong DB
        $realCount = $this->rooms()->count();

        // Cập nhật vào cột total_quantity
        $this->total_quantity = $realCount;
        $this->save();

        return $realCount;
    }

    /**
     * Static Method: Gọi hàm này để tính lại cho TOÀN BỘ các loại phòng
     * Cách dùng: RoomType::syncAllQuantities();
     */
    public static function syncAllQuantities()
    {
        $types = self::all();
        foreach ($types as $type) {
            $type->refreshQuantity();
        }
        return "Đã đồng bộ xong số lượng phòng cho " . $types->count() . " loại phòng.";
    }

    protected static function booted()
    {
        static::updated(function ($roomType) {
            if ($roomType->isDirty('total_quantity')) {
                // Tự động update cột available_quantity của tất cả RoomOption con
                $roomType->room_options()->update([
                    'available_quantity' => $roomType->total_quantity
                ]);
            }
        });
    }
}
