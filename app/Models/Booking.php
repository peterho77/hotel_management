<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = "booking";

    public function room_booking_items()
    {
        return $this->hasMany(RoomBookingItem::class, 'booking_id');
    }
}
