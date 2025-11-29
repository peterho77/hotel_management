<?php

namespace App\Services\EmailSMTP;

use App\Models\Booking;
use App\Services\EmailSMTP\BookingConfirmationMail;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Gửi email xác nhận đơn hàng cho khách hàng
     * 
     * @param Booking $booking
     * @return bool
     */
    public function sendBookingConfirmation(Booking $booking): bool
    {
        // Chỉ gửi nếu có email
        if (empty($booking->customer->email)) {
            return false;
        }

        try {
            Mail::to($booking->customer->email)
                ->send(new BookingConfirmationMail($booking));

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}