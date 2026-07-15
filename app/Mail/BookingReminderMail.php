<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class BookingReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reminder: Your booking is tomorrow — {$this->booking->booking_code}"
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-reminder',
        );
    }

    // No attachments needed for reminder
    public function attachments(): array
    {
        return [];
    }
}
?>
