<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfferingCategory extends Model
{
    protected $fillable = ['offering_id', 'name', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function offering(): BelongsTo
    {
        return $this->belongsTo(Offering::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OfferingItem::class)->orderBy('sort_order')->orderBy('id');
    }

    public function getTotalCostAttribute(): float
    {
        return (float) $this->items->sum('total_cost');
    }

    public function getTotalCostFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total_cost, 0, ',', '.');
    }
}
