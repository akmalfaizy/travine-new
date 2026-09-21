<?php

namespace App\Mail;

use App\Models\Partner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnerResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public Partner $partner;
    public string $token;

    public function __construct(Partner $partner, string $token)
    {
        $this->partner = $partner;
        $this->token   = $token;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password Akun Partner travine.id',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.partner-reset-password',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
