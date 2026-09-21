<?php

namespace App\Mail;

use App\Models\ProjectInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProjectInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ProjectInvoice $projectInvoice,
        public $pdfData,
        public ?string $emailMessage = null
    ) {}

    public function envelope(): Envelope
    {
        $status = $this->projectInvoice->payment_status;
        $subject = match($status) {
            'paid'      => "Kuitansi & Bukti Pelunasan Invoice — {$this->projectInvoice->invoice_number} | travine.id",
            'partial'   => "Konfirmasi Pembayaran Sebagian & Update Invoice — {$this->projectInvoice->invoice_number} | travine.id",
            'overdue'   => "Pengingat Jatuh Tempo Pembayaran Invoice — {$this->projectInvoice->invoice_number} | travine.id",
            'cancelled' => "Pemberitahuan Pembatalan Invoice — {$this->projectInvoice->invoice_number} | travine.id",
            default     => "Tagihan Invoice Resmi Project — {$this->projectInvoice->invoice_number} | travine.id",
        };

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.project-invoice',
        );
    }

    public function attachments(): array
    {
        $filename = 'Invoice-' . str_replace(['/', '\\'], '-', $this->projectInvoice->invoice_number) . '.pdf';

        return [
            Attachment::fromData(fn () => $this->pdfData, $filename)
                ->withMime('application/pdf'),
        ];
    }
}
