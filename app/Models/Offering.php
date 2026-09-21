<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Offering extends Model
{
    protected $fillable = [
        'offering_number',
        'client_name',
        'client_email',
        'provider',
        'pic_client',
        'pic_project',
        'pic_contact',
        'package_name',
        'date_duration',
        'participants',
        'description',
        'production_cost',
        'other_cost_percentage',
        'other_cost',
        'management_fee_percentage',
        'management_fee',
        'total_amount',
        'price_per_pack',
        'status',
        'notes',
        'revision_notes',
        'recommendation',
    ];

    protected $casts = [
        'participants'              => 'integer',
        'production_cost'           => 'float',
        'other_cost_percentage'     => 'float',
        'other_cost'                => 'float',
        'management_fee_percentage' => 'float',
        'management_fee'            => 'float',
        'total_amount'              => 'float',
        'price_per_pack'            => 'float',
        'notes'                     => 'array',
        'revision_notes'            => 'array',
    ];

    public static function generateOfferingNumber(): string
    {
        $year = date('Y');
        $prefix = "TRV/OFF/{$year}/";
        $latest = static::where('offering_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($latest && preg_match('/(\d+)$/', $latest->offering_number, $matches)) {
            $next = intval($matches[1]) + 1;
        } else {
            $next = 1;
        }

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(OfferingCategory::class)->orderBy('sort_order')->orderBy('id');
    }

    public function items(): HasMany
    {
        return $this->hasManyThrough(OfferingItem::class, OfferingCategory::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(ProjectInvoice::class);
    }

    public function latestInvoice(): HasOne
    {
        return $this->hasOne(ProjectInvoice::class)->latestOfMany();
    }

    // Accessors
    public function getProductionCostFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->production_cost, 0, ',', '.');
    }

    public function getOtherCostFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->other_cost, 0, ',', '.');
    }

    public function getManagementFeeFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->management_fee, 0, ',', '.');
    }

    public function getTotalAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getPricePerPackFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->price_per_pack, 0, ',', '.');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'draft'    => ['label' => 'Draft', 'class' => 'badge-muted', 'bg' => '#64748b'],
            'sent'     => ['label' => 'Sent', 'class' => 'badge-info', 'bg' => '#3b82f6'],
            'revised'  => ['label' => 'Revised', 'class' => 'badge-warning', 'bg' => '#f59e0b'],
            'approved' => ['label' => 'Approved', 'class' => 'badge-success', 'bg' => '#10b981'],
            'rejected' => ['label' => 'Rejected', 'class' => 'badge-danger', 'bg' => '#ef4444'],
            'expired'  => ['label' => 'Expired', 'class' => 'badge-muted', 'bg' => '#94a3b8'],
            default    => ['label' => ucfirst($this->status), 'class' => 'badge-info', 'bg' => '#3b82f6'],
        };
    }
}
