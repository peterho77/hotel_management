<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RoomBookingItem extends Model
{
    protected $table = "room_booking_item";
    protected $fillable = [
        'booking_id',
        'room_option_id',
        'assigned_room_id',
        'applied_discount_id',
        'total_base_price',
        'discount_amount',
        'final_price',
    ];
    public function room_option()
    {
        return $this->belongsTo(RoomOption::class, 'room_option_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function assigned_room()
    {
        return $this->belongsTo(Room::class, 'assigned_room_id');
    }
}
