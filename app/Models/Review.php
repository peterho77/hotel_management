<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Review extends Model
{
    protected $table = 'review';

    protected $fillable = [
        'booking_id',
        'rating',
        'general_review',
        'positive',
        'negative'
    ];
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function images()
    {
        return $this->hasMany(Images::class, 'review_id');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)
            ->timezone('Asia/Ho_Chi_Minh')
            ->format('H:i  d/m/Y');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)
            ->timezone('Asia/Ho_Chi_Minh')
            ->format('H:i  d/m/Y');
    }
}
