<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Discount extends Model
{
    protected $table = 'discount';
    public function room_rate_options(): BelongsToMany
    {
        return $this->belongsToMany(
            RoomRateOption::class,
            "room_discount_detail",
            'discount_id',
            'room_rate_option_id'
        );
    }
    public function room_booking_items(): BelongsToMany
    {
        return $this->belongsToMany(
            RoomRateOption::class,
            "room_discount_detail",
            'discount_id',
            'booking_item_id'
        );
    }
}
