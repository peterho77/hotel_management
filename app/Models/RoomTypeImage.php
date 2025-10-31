<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomTypeImage extends Model
{
    protected $fillable = ['room_type_id', 'path', 'is_featured','sort_odrder','alt_text'];

    public function roomType()
    {
        return $this->belgongsTo(RoomType::class, 'room_type_id');
    }
}
