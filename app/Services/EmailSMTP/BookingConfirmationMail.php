<?php

namespace App\Services\EmailSMTP;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;
    public $booking;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        if (!$booking->relationLoaded('payments')) {
            $booking->load('payments');
        }
        if (!$booking->relationLoaded('customer')) {
            $booking->load('customer');
        }
    }

    /**
     * Build the message.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác nhận đặt phòng #' . $this->booking->id . ' - Sona Hotel',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bookingConfirmation',
            with: [
                'booking' => $this->booking,
            ]
        );
    }
}
