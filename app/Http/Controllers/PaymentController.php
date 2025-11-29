<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Payment\VNPayService;
use App\Services\EmailSMTP\EmailService;
use App\Models\Booking;

class PaymentController extends Controller
{
    protected VNPayService $vnpayService;
    protected EmailService $emailService;

    public function __construct(VNPayService $vnpayService, EmailService $emailService)
    {
        $this->vnpayService = $vnpayService;
        $this->emailService = $emailService;
    }

    // init VNPAY payment 
    public function vnpayCheckout(int $booking_id)
    {
        $booking = Booking::findOrFail($booking_id);
        $url = $this->vnpayService->generatePaymentUrl($booking);

        return redirect()->away($url);
    }

    // return URL from VNPAY
    public function vnpayReturn(Request $request)
    {
        $result = $this->vnpayService->processReturn($request->all());
        if ($result['success']) {
            try {
                $booking = Booking::find($result['bookingId']);
                $this->emailService->sendBookingConfirmation($booking);
            } catch (\Exception $e) {
                Log::error('Failed to send order confirmation email after VNPay payment', [
                    'booking_id' => $result['bookingId'],
                    'error' => $e->getMessage()
                ]);
            }
            return redirect()->route('booking.index', ['success' => $result['message']]);
        }
        return redirect()->route('booking.index')->with('error', $result['message']);
    }

    public function vnpayIpn(Request $request)
    {
        $result = $this->vnpayService->processReturn($request->all());
        return response()->json([
            'RspCode' => $result['success'] ? '00' : '97',
            'Message' => $result['message'] ?? 'Invalid signature',
        ]);
    }
}
