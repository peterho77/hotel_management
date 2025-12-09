<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RoomBookingItem extends Model
{
    protected $table = "room_booking_item";
    public function room_option()
    {
        return $this->belongsTo(RoomOption::class, 'room_option_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
