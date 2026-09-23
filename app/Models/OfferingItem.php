<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferingItem extends Model
{
    protected $fillable = [
        'offering_category_id',
        'component',
        'description',
        'remark',
        'type',
        'quantity',
        'unit_price',
        'total_cost',
        'sort_order',
    ];

    protected $casts = [
        'quantity'   => 'float',
        'unit_price' => 'float',
        'total_cost' => 'float',
        'sort_order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(OfferingCategory::class, 'offering_category_id');
    }

    public function getUnitPriceFormattedAttribute(): string
    {
        return ($this->unit_price !== null && $this->unit_price !== '') 
            ? 'Rp ' . number_format($this->unit_price, 0, ',', '.') 
            : '-';
    }

    public function getTotalCostFormattedAttribute(): string
    {
        return ($this->total_cost !== null && $this->total_cost !== '') 
            ? 'Rp ' . number_format($this->total_cost, 0, ',', '.') 
            : '-';
    }

    public function isOptional(): bool
    {
        return stripos($this->remark ?? '', 'optional') !== false;
    }
}
