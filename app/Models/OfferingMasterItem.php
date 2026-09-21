<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferingMasterItem extends Model
{
    protected $fillable = [
        'category_name',
        'component',
        'description',
        'default_type',
        'default_remark',
        'default_price',
    ];

    protected $casts = [
        'default_price' => 'float',
    ];

    public function getDefaultPriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->default_price, 0, ',', '.');
    }
}
