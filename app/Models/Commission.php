<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id', 'booking_id', 'tour_id',
        'hpp_snapshot', 'publish_price_snapshot', 'margin_snapshot',
        'commission_percentage_snapshot', 'commission_amount', 'status'
    ];

    protected $casts = [
        'hpp_snapshot'                   => 'integer',
        'publish_price_snapshot'         => 'integer',
        'margin_snapshot'                => 'integer',
        'commission_percentage_snapshot' => 'float',
        'commission_amount'              => 'integer',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function getAmountAttribute(): int
    {
        return (int) $this->commission_amount;
    }

    public function getCommissionAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->commission_amount, 0, ',', '.');
    }

    public function getPublishPriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->publish_price_snapshot, 0, ',', '.');
    }

    public function getHppFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->hpp_snapshot, 0, ',', '.');
    }

    public function getMarginFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->margin_snapshot, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'pending'    => 'Menunggu Konfirmasi Order',
            'approved'   => 'Disetujui',
            'available'  => 'Tersedia',
            'requested'  => 'Dalam Pengajuan Pencairan',
            'processing' => 'Diproses Admin',
            'paid'       => 'Dicairkan',
            'rejected'   => 'Ditolak',
            'cancelled'  => 'Dibatalkan',
        ][$this->status] ?? $this->status;
    }
}
