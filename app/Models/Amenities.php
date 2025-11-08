<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Amenities extends Model
{
    public function room_types(): BelongsToMany
    {
        return $this->belongsToMany(RoomType::class, 'room_type_amenities', 'amenity_id', 'room_type_id');
    }
}
