<?php

namespace App\Mail;

use App\Models\Offering;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class OfferingProposalMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Offering $offering,
        public $pdfData,
        public ?string $emailMessage = null,
        public array $ccEmails = []
    ) {}

    public function envelope(): Envelope
    {
        $cc = array_map(fn($email) => new Address(trim($email)), array_filter($this->ccEmails));

        return new Envelope(
            subject: "Proposal Penawaran Biaya Produksi — {$this->offering->offering_number} | travine.id",
            cc: $cc,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.offering-proposal',
        );
    }

    public function attachments(): array
    {
        $filename = 'Offering-' . str_replace(['/', '\\'], '-', $this->offering->offering_number) . '.pdf';

        return [
            Attachment::fromData(fn () => $this->pdfData, $filename)
                ->withMime('application/pdf'),
        ];
    }
}
