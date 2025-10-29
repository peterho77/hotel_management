<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Guest/Booking', [
            'checkIn' => $request->query('checkIn'),
            'checkOut' => $request->query('checkOut'),
            'num_of_guests' => (int) $request->query('num_of_guests'),
            'num_of_rooms' => (int) $request->query('num_of_rooms'),
        ]);
    }
}
