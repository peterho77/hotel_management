<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Discount extends Model
{
    protected $table = 'discount';
    public function room_types(): BelongsToMany
    {
        return $this->belongsToMany(
            RoomRateOption::class,
            "room_rate_discount",
            'discount_id',
            'room_rate_option_id'
        );
    }
    public function room_booking_items(): BelongsToMany
    {
        return $this->belongsToMany(
            RoomRateOption::class,
            "booking_item_discount_detail",
            'discount_id',
            'booking_item_id'
        );
    }
}
