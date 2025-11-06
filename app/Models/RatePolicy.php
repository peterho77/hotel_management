<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RatePolicy extends Model
{
    public function room_rate_option()
    {
        return $this->belongsTo(RoomRateOption::class);
    }
}
