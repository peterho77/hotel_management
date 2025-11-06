<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RoomBookingItem extends Model
{
    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(
            Discount::class,
            'booking_item_discount_detail',
            'booking_item_id',
            'discount_id'
        );
    }
}
