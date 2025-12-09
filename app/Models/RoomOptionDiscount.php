<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomOptionDiscount extends Model
{
    //
    protected $table = 'room_option_discount';

    protected $fillable = [
        'room_option_id',
        'discount_id',
        'applied_amount',
        'applied_order',
        'discount_applied',
        'is_active',
    ];
}
