<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomDiscountDetail extends Model
{
    //
    protected $table = 'room_discount_detail';

    protected $fillable = [
        'room_rate_option_id',
        'discount_id',
        'applied_amount',
        'applied_order',
        'discount_applied',
        'is_active',
    ];
}
