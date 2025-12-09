<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomOptionRatePolicy extends Model
{
    //
    protected $table = 'room_option_rate_policy';

    public function room_option()
    {
        return $this->belongsTo(RoomOption::class);
    }

    public function rate_policy()
    {
        return $this->belongsTo(RatePolicy::class);
    }
}
