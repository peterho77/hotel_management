<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RoomRateOption extends Model
{
    protected $table="room_rate_option";
    public function room_type()
    {
        return $this->belongsTo(RoomType::class);
    }
    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(
            Discount::class,
            'rate_option_discount',
            'room_rate_option_id',
            'discount_id'
        );
    }
    public function rate_policy(): HasMany
    {
        return $this->hasMany(RatePolicy::class, 'rate_policy_id', 'id');
    }
}
