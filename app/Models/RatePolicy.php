<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RatePolicy extends Model
{
    protected $table = "rate_policy";
    public function room_rate_options(): HasMany
    {
        return $this->hasMany(RoomRateOption::class, 'rate_policy_id', 'id');
    }
}
