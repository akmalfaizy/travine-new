<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerClick extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id', 'referral_code', 'landing_url', 'ip_address', 'user_agent'
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
