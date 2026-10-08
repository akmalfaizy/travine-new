<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'session_id',
        'path',
        'url',
        'method',
        'referer',
        'user_agent',
        'device_type',
        'browser',
        'platform',
        'country',
        'country_code',
        'province',
        'city',
        'postal_code',
        'is_bot',
        'visited_at',
    ];

    protected $casts = [
        'is_bot'     => 'boolean',
        'visited_at' => 'datetime',
    ];

    public function scopeNonBots($query)
    {
        return $query->where('is_bot', false);
    }

    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('visited_at', [$startDate, $endDate]);
    }
}
