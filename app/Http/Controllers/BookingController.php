<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Models\RoomType;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Booking;

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
        $roomBookingDetail = $request->validate([
            'date_range' => 'required',
            'num_adults' => 'required|integer',
            'num_children' => 'required|integer',
            'num_rooms' => 'required|integer',
            'num_nights' => 'required|integer',
            'selected_rooms' => 'required|array',
        ]);

        return Inertia::render('Guest/Booking-infor', [
            'roomBookingDetail' => $roomBookingDetail
        ]);
    }

    public function confirm(Request $request)
    {
        $bookingInfor = $request->validate([
            'check_in' => 'required',
            'check_out' => 'required',
            'num_rooms' => 'required|integer',
            'num_nights' => 'required|integer',
            'num_adults' => 'required|integer',
            'num_children' => 'required|integer',
            'total_price' => 'required',
        ]);

        // change format check in and check out: Y-m-d
        $bookingInfor['check_in'] = Carbon::createFromFormat('d/m/Y', $bookingInfor['check_in'])->format('Y-m-d');
        $bookingInfor['check_out'] = Carbon::createFromFormat('d/m/Y', $bookingInfor['check_out'])->format('Y-m-d');

        // add required booking field
        $bookingInfor['status'] = 'pending';
        $bookingInfor['payment_option'] = 'online';

        // customer
        $customerInfor = $request->validate([
            'full_name' => 'required',
            'email' => 'required',
            'phone' => 'required',
        ]);

        Customer::firstOrCreate(
            [
                'email' => $customerInfor['email'],
                'phone' => $customerInfor['phone']
            ],
            [
                'full_name' => $customerInfor['full_name'],
                'country' => $customerInfor['country'] ?? 'Việt Nam'
            ]
        );

        $newBooking = Booking::create($bookingInfor);

        $newBooking->payments()->create([
            'type' => 'e_wallet',
            'instrument' => 'vnpay',
            'status' => 'pending',
            'transaction_type' => 'full',
            'amount' => $newBooking->total_price,
        ]);

        return response()->json([
            'redirect_url' => route('payment.vnpay', $newBooking->id)
        ]);
    }

    
}
