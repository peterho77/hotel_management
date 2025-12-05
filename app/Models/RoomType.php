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
    public function images() :HasMany   
    {
        return $this->hasMany(Images::class, 'room_type_id');
    }
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenities::class, 'room_type_amenities', 'room_type_id','amenity_id');
    }
    public function room_rate_options(): HasMany
    {
        return $this->hasMany(RoomRateOption::class, 'room_type_id', 'id');
    }
    protected $fillable = ['name', 'description', 'max_adults', 'max_children', 'total_quantity', 'status', 'branch_id'];
}
