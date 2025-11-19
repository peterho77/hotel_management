<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = "booking";

    protected $fillable = [
        'customer_id',
        'check_in_date',
        'check_out_date',
        'num_nights',
        'num_rooms',
        'num_adults',
        'num_children',
        'total_price',
        'amount_paid',
        'deposit_amount',
        'booking_status',
        'payment_status',
        'payment_method',
        'payment_instrument',
        'special_request'
    ];

    public function room_booking_items()
    {
        return $this->hasMany(RoomBookingItem::class, 'booking_id');
    }
}
