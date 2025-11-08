<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomRateOption extends Model
{
    protected $table = "room_rate_option";
    protected $appends = ['price'];
    public function room_type()
    {
        return $this->belongsTo(RoomType::class);
    }
    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(
            Discount::class,
            'rate_option_discount',
            'room_rate_option_id',
            'discount_id'
        );
    }
    public function rate_policy(): BelongsTo
    {
        return $this->belongsTo(RatePolicy::class, 'rate_policy_id', 'id');
    }

    public function getPriceAttribute()
    {
        $roomType = $this->room_type;
        if (!$roomType) {
            return null;
        }

        $basePrice = $roomType->base_price;

        if (isset($this->rate_policy['is_refundable']) && !$this->rate_policy['is_refundable']) {
            $basePrice = $basePrice * 0.95;
        }

        return max($basePrice, 0);
    }
}
