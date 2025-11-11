<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomRateDiscount extends Model
{
    //
    protected $table = 'room_rate_discount';

    protected $fillable = [
        'room_rate_option_id',
        'discount_id',
        'applied_value',
        'applied_order',
        'discount_applied',
        'is_active',
    ];

    public function room_rate_option()
    {
        return $this->belongsTo(RoomRateOption::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
}
