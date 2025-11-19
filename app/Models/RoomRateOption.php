<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomRateOption extends Model
{
    protected $table = "room_rate_option";
    protected $appends = ['price', 'final_price'];
    public function room_type()
    {
        return $this->belongsTo(RoomType::class);
    }
    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(
            Discount::class,
            'room_discount_detail',
            'room_rate_option_id',
            'discount_id'
        )->withPivot(['applied_amount', 'applied_order', 'discount_applied', 'is_active']);
    }
    public function rate_policies(): BelongsToMany
    {
        return $this->belongsToMany(
            RatePolicy::class,
            'room_rate_policy',
            'room_rate_option_id',
            'rate_policy_id'
        );
    }

    public function room_booking_items()
    {
        return $this->hasMany(RoomBookingItem::class, 'room_rate_option_id');
    }

    public function getPriceAttribute()
    {
        $roomType = $this->room_type;
        if (!$roomType) {
            return null;
        }

        $basePrice = $roomType->base_price;

        if (isset($this->rate_policies['is_refundable']) && !$this->rate_policies['is_refundable']) {
            $basePrice = $basePrice * 0.95;
        }

        return max($basePrice, 0);
    }

    public function getFinalPriceAttribute()
    {
        $basePrice = $this->price; // accessor hiện tại

        // Lấy pivot discounts đã active và theo applied_order
        $totalDiscount = $this->discounts()
            ->get()
            ->where('pivot.is_active', true)
            ->sortBy('pivot.applied_order')
            ->sum('pivot.applied_amount');

        return max($basePrice - $totalDiscount, 0);
    }
}
