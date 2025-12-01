<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\RoomType;

class GuestController extends Controller
{
    public function rooms()
    {
        $roomTypeList = RoomType::with(['images', 'amenities'])->get();
        return Inertia::render('Guest/Room', [
            'roomTypeList' => $roomTypeList,
        ]);
    }

    public function roomDetail(int $id)
    {
        $roomType = RoomType::with('images')->findOrFail($id);

        return Inertia::render('Guest/Room-detail', [
            'roomType' => $roomType
        ]);
    }
}
