<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'name', 'category', 'phone', 'email', 'location', 'pic_name', 'notes', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static array $categories = [
        'Bus / Transportasi'  => 'Bus / Transportasi',
        'Hotel / Penginapan'  => 'Hotel / Penginapan',
        'Restoran / Katering' => 'Restoran / Katering',
        'Wisata / Tiket'      => 'Wisata / Tiket',
        'Guide / Pemandu'     => 'Guide / Pemandu',
        'Dokumentasi'         => 'Dokumentasi',
        'Lainnya'             => 'Lainnya',
    ];

    public function products()
    {
        return $this->hasMany(VendorProduct::class)->orderBy('name');
    }

    public function getIsActiveLabelAttribute(): string
    {
        return $this->is_active ? 'Aktif' : 'Non-Aktif';
    }
}
