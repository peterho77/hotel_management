<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\RoomType;
use App\Models\Review;
use App\Models\CustomerType;

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
        $roomType = RoomType::with(['images', 'amenities'])->findOrFail($id);

        return Inertia::render('Guest/Room-detail', [
            'roomType' => $roomType
        ]);
    }

    public function review()
    {
        $customerTypeList = CustomerType::get();
        $reviewList = Review::with(['booking.customer', 'booking.room_booking_items.room_option'])->get();

        return Inertia::render('Guest/Review', [
            'customerTypeList' => $customerTypeList,
            'reviewList' => $reviewList
        ]);
    }
}
