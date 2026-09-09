<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Partner extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'referral_code',
        'email_verified_at', 'status', 'address', 'city', 'province',
        'bank_name', 'bank_account_name', 'bank_account_number',
        'otp_code', 'otp_expires_at', 'otp_attempts', 'otp_sent_at'
    ];

    protected $hidden = [
        'password', 'remember_token', 'otp_code'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at'    => 'datetime',
            'otp_sent_at'       => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public static function generateReferralCode(?string $name = null): string
    {
        $prefix = 'TRV-';
        if ($name) {
            $cleanName = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', strtok($name, ' ')));
            if (strlen($cleanName) >= 3) {
                $prefix .= substr($cleanName, 0, 5) . '-';
            }
        }

        do {
            $code = $prefix . strtoupper(Str::random(6));
        } while (static::where('referral_code', $code)->exists());

        return $code;
    }

    public function isProfileComplete(): bool
    {
        return !empty($this->bank_name) &&
               !empty($this->bank_account_name) &&
               !empty($this->bank_account_number);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function clicks()
    {
        return $this->hasMany(PartnerClick::class);
    }

    public function getReferralLinkAttribute(): string
    {
        return url('/?ref=' . $this->referral_code . '#kategori-wisata');
    }

    public function getAvailableCommissionAttribute(): float
    {
        return (float) $this->commissions()->where('status', 'available')->sum('commission_amount');
    }

    public function getTotalCommissionAttribute(): float
    {
        return (float) $this->commissions()
            ->whereIn('status', ['available', 'requested', 'processing', 'paid'])
            ->sum('commission_amount');
    }

    public function getProcessingCommissionAttribute(): float
    {
        return (float) $this->withdrawals()
            ->whereIn('status', ['requested', 'approved', 'processing'])
            ->sum('amount');
    }

    public function getPaidCommissionAttribute(): float
    {
        return (float) $this->withdrawals()
            ->where('status', 'paid')
            ->sum('amount');
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'pending'   => 'Menunggu Verifikasi',
            'active'    => 'Aktif',
            'suspended' => 'Ditangguhkan',
            'inactive'  => 'Nonaktif',
        ][$this->status] ?? $this->status;
    }
}
