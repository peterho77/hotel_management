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
    public function images()
    {
        return $this->hasMany(RoomTypeImage::class, 'room_type_id');
    }
    protected $fillable = ['name', 'description', 'max_adults', 'max_children', 'quantity', 'base_price_per_night', 'hourly_rate', 'full_day_rate', 'overnight_rate', 'amenities', 'status', 'branch_id'];
}
