<?php

namespace App\Mail;

use App\Models\Partner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnerOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public Partner $partner;
    public string $otp;

    public function __construct(Partner $partner, string $otp)
    {
        $this->partner = $partner;
        $this->otp     = $otp;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode Verifikasi OTP Akun Partner travine.id',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.partner-otp',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
