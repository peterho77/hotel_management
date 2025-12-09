<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RatePolicy extends Model
{
    protected $table = "rate_policy";
    public function room_rate_options(): BelongsToMany
    {
        return $this->belongsToMany(
            RoomOption::class,
            'room_option_rate_policy',
            'rate_policy_id',
            'room_option_id'
        );
    }
}
