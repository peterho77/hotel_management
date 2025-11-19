<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\RoomType;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $roomTypeList = RoomType::with(['images', 'room_rate_options.rate_policies', 'room_rate_options.discounts', 'amenities'])->get();
        return Inertia::render('Guest/Booking', [
            'checkIn' => $request->query('checkIn'),
            'checkOut' => $request->query('checkOut'),
            'num_of_guests' => (int) $request->query('num_of_guests'),
            'num_of_rooms' => (int) $request->query('num_of_rooms'),
            'roomTypeList' => $roomTypeList,
        ]);
    }

    public function detail(Request $request)
    {
        $detail = $request->validate([
            'date_range' => 'required',
            'num_adults' => 'required|integer',
            'num_children' => 'required|integer',
            'num_rooms' => 'required|integer',
            'num_nights' => 'required|integer',
            'selected_rooms' => 'required|array',
        ]);

        return Inertia::render('Guest/Booking-infor',[
            'detail' => $detail
        ]);
    }

    public function confirm(Request $request)
    {
        
    }
}
