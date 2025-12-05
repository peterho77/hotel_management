<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    protected $fillable = [
        'room_type_id',
        'review_id',
        'path',
        'is_featured',
        'sort_odrder',
        'alt_text'
    ];

    public function roomType()
    {
        return $this->belgongsTo(RoomType::class, 'room_type_id');
    }

    public function review()
    {
        return $this->belgongsTo(Review::class, 'review_id');
    }
}
