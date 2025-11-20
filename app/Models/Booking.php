<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = "booking";

    protected $fillable = [
        'customer_id',
        'check_in',
        'check_out',
        'num_nights',
        'num_rooms',
        'num_adults',
        'num_children',
        'total_price',
        'amount_paid',
        'deposit_amount',
        'status',
        'payment_option',
        'special_request'
    ];

    public function room_booking_items()
    {
        return $this->hasMany(RoomBookingItem::class, 'booking_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
