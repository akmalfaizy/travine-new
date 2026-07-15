<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function sendBookingConfirmation(Booking $booking): bool
    {
        $invoiceUrl = url('/bookings/' . $booking->booking_code . '/invoice');

        $message = "Halo *{$booking->name}*,\n\n";
        $message .= "Pemesanan wisata Anda telah *dikonfirmasi*.\n\n";
        $message .= "📋 *Detail Pemesanan:*\n";
        $message .= "Nomor Invoice: *{$booking->invoice_number}*\n";
        $message .= "Kode Booking: *{$booking->booking_code}*\n";
        $message .= "Paket Wisata: *{$booking->tour->title}*\n";
        $message .= "Tanggal Keberangkatan: *{$booking->departure_date->format('d M Y')}*\n";
        $message .= "Jumlah Peserta: *{$booking->participants} orang*\n";
        $message .= "Total Biaya: *{$booking->total_price_formatted}*\n\n";
        $message .= "📄 Invoice Anda: {$invoiceUrl}\n\n";
        $message .= "Terima kasih telah memilih travine.id! 🌏";

        return $this->send($booking, $booking->whatsapp, $message);
    }

    public function sendStatusUpdate(Booking $booking, string $statusLabel): bool
    {
        $message = "Halo *{$booking->name}*,\n\n";
        $message .= "Status pemesanan Anda telah diperbarui.\n\n";
        $message .= "Kode Booking: *{$booking->booking_code}*\n";
        $message .= "Paket Wisata: *{$booking->tour->title}*\n";
        $message .= "Status Baru: *{$statusLabel}*\n\n";
        $message .= "Info lebih lanjut: " . url('/bookings/' . $booking->booking_code) . "\n\n";
        $message .= "Terima kasih. — travine.id 🌏";

        return $this->send($booking, $booking->whatsapp, $message);
    }

    private function send(Booking $booking, string $phone, string $message): bool
    {
        $apiUrl   = config('services.whatsapp.url');
        $apiToken = config('services.whatsapp.token');

        $log = NotificationLog::create([
            'booking_id' => $booking->id,
            'recipient'  => $phone,
            'type'       => 'WhatsApp',
            'status'     => 'success',
            'sent_at'    => now(),
        ]);

        // If not configured, just log it (mock mode)
        if (empty($apiUrl) || empty($apiToken)) {
            Log::info("[WhatsApp Mock] To: {$phone}\n{$message}");
            return true;
        }

        try {
            $response = Http::withToken($apiToken)->post($apiUrl, [
                'phone'   => $phone,
                'message' => $message,
            ]);

            if (!$response->successful()) {
                $log->update(['status' => 'failed', 'error_message' => $response->body()]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            $log->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            return false;
        }
    }
}
