<?php

namespace App\Mail;

use App\Models\Withdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WithdrawalStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public Withdrawal $withdrawal;

    public function __construct(Withdrawal $withdrawal)
    {
        $this->withdrawal = $withdrawal;
    }

    public function envelope(): Envelope
    {
        $statusLabels = [
            'approved'   => 'Disetujui',
            'processing' => 'Sedang Diproses',
            'paid'       => 'Telah Dibayarkan!',
            'rejected'   => 'Ditolak',
        ];
        $label = $statusLabels[$this->withdrawal->status] ?? $this->withdrawal->status;

        return new Envelope(
            subject: 'Status Penarikan Komisi #' . $this->withdrawal->withdrawal_code . ': ' . $label,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.partner-withdrawal-status',
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        if ($this->withdrawal->status === 'paid' && $this->withdrawal->transfer_proof) {
            $proofPath = storage_path('app/public/' . $this->withdrawal->transfer_proof);
            if (file_exists($proofPath)) {
                $attachments[] = Attachment::fromPath($proofPath)
                    ->as('Bukti-Transfer-' . $this->withdrawal->withdrawal_code . '.' . pathinfo($proofPath, PATHINFO_EXTENSION));
            }
        }
        return $attachments;
    }
}
