<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'tour_id', 'booking_code', 'invoice_number', 'invoice_category', 'name', 'whatsapp',
        'email', 'departure_date', 'duration', 'participants', 'total_price', 'discount', 'notes',
        'payment_proof', 'payment_status', 'payment_type', 'paid_amount', 'booking_status', 'admin_notes'
    ];

    protected $casts = [
        'departure_date' => 'date',
        'total_price'    => 'integer',
        'discount'       => 'integer',
        'paid_amount'    => 'integer',
    ];

    public static function generateBookingCode(): string
    {
        $date = now()->format('Ymd');
        $count = static::whereDate('created_at', today())->count() + 1;
        return 'BOOK-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public static function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $count = static::whereNotNull('invoice_number')->whereDate('created_at', today())->count() + 1;
        return 'INV-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function notificationLogs()
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function getTotalPriceFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getDiscountFormattedAttribute()
    {
        return 'Rp ' . number_format($this->discount, 0, ',', '.');
    }

    public function getPaidAmountFormattedAttribute()
    {
        return 'Rp ' . number_format($this->paid_amount, 0, ',', '.');
    }

    public function getRemainingAmountAttribute()
    {
        return max(0, $this->total_price - $this->paid_amount);
    }

    public function getDurationDaysAttribute()
    {
        if ($this->duration && preg_match('/(\d+)/', $this->duration, $matches)) {
            return (int) $matches[1];
        }
        if ($this->tour?->duration && preg_match('/(\d+)/', $this->tour->duration, $matches)) {
            return (int) $matches[1];
        }
        return 1;
    }

    public function getPaymentStatusLabelAttribute()
    {
        return [
            'unpaid'  => 'Belum Bayar',
            'dp_paid' => 'DP Dibayar',
            'paid'    => 'Lunas',
            'refund'  => 'Refund',
            'failed'  => 'Gagal',
        ][$this->payment_status] ?? $this->payment_status;
    }

    public function getBookingStatusLabelAttribute()
    {
        return [
            'waiting_payment'      => 'Menunggu Pembayaran',
            'waiting_verification' => 'Menunggu Verifikasi',
            'processing'           => 'Diproses',
            'confirmed'            => 'Dikonfirmasi',
            'departed'             => 'Berangkat',
            'completed'            => 'Selesai',
            'cancelled'            => 'Dibatalkan',
        ][$this->booking_status] ?? $this->booking_status;
    }
}
