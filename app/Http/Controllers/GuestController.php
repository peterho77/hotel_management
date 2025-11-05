<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\RoomType;

class GuestController extends Controller
{
    public function rooms()
    {
        $roomTypeList = RoomType::with(['images'])->get();
        return Inertia::render('Guest/Room', [
            'roomTypeList' => $roomTypeList,
        ]);
    }
}
