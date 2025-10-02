<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    protected $table = "room";
    public $timestamps = false;

    public function room_type(): BelongsTo
    {
        return $this->belongsTo(RoomType::class)->withDefault([
            'name' => 'Chưa xếp hạng phòng',
        ]);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withDefault([
            'name' => 'Chưa có chi nhánh',
        ]);
    }

    protected $fillable = ['name', 'area', 'status', 'note', 'branch_id', 'room_type_id'];
}
