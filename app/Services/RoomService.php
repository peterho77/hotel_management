<?php

namespace App\Services;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\Branch;

class RoomService
{
    protected $room;

    public function __construct(Room $room)
    {
        $this->room = $room;
    }

    public function getAllData()
    {
        $roomList = $this->room::with(['branch', 'room_type'])
            ->get()
            ->makeHidden(['branch_id', 'room_type_id']);

        $columns = [];
        if ($roomList->isNotEmpty()) {
            $columns = array_diff(
                array_keys($roomList->first()->getAttributes()), 
                ['branch_id', 'room_type_id']
            );
        }

        return [
            'roomList'     => $roomList,
            'roomTypeList' => RoomType::all(),
            'branchList'   => Branch::all(),
            'roomColumns'  => array_values($columns),
        ];
    }

    public function createRoom(array $data)
    {
        return $this->room::create($data);
    }

    public function updateRoom($id, array $data)
    {
        $room = $this->room::findOrFail($id);
        return $room->update($data);
    }

    public function deleteRoom($id)
    {
        $room = $this->room::findOrFail($id);
        return $room->delete();
    }
}