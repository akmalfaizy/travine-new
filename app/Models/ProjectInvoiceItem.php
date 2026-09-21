<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectInvoiceItem extends Model
{
    protected $fillable = [
        'project_invoice_id',
        'category_name',
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

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(ProjectInvoice::class, 'project_invoice_id');
    }

    public function getUnitPriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->unit_price, 0, ',', '.');
    }

    public function getTotalCostFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total_cost, 0, ',', '.');
    }
}
