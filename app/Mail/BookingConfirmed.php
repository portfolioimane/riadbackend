<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Booking;

class BookingConfirmed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $booking;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking)
    {
        // Make sure the room relation is loaded
        $this->booking = $booking->loadMissing('room');
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Booking Confirmation')
                    ->markdown('emails.booking.confirmed')
                    ->with([
                        'booking' => $this->booking, // explicitly pass booking with room loaded
                    ]);
    }
}
