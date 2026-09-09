<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id', 'withdrawal_code', 'amount', 'bank_name',
        'bank_account_name', 'bank_account_number', 'status',
        'rejection_reason', 'transfer_proof', 'admin_note', 'paid_at'
    ];

    protected $casts = [
        'amount'  => 'integer',
        'paid_at' => 'datetime',
    ];

    public static function generateCode(): string
    {
        $date = now()->format('Ymd');
        $count = static::whereDate('created_at', today())->count() + 1;
        return 'WD-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function getAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'requested'  => 'Menunggu Review',
            'approved'   => 'Disetujui',
            'processing' => 'Diproses Transfer',
            'paid'       => 'Dibayar',
            'rejected'   => 'Ditolak',
        ][$this->status] ?? $this->status;
    }
}
