<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectInvoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'offering_id',
        'client_name',
        'client_email',
        'client_company',
        'pic_client',
        'pic_contact',
        'project_name',
        'description',
        'issue_date',
        'due_date',
        'subtotal',
        'other_cost',
        'management_fee',
        'gross_total',
        'pph_percentage',
        'pph_amount',
        'net_amount',
        'include_pph_deduction',
        'amount_paid',
        'remaining_balance',
        'payment_status',
        'bank_name',
        'bank_account',
        'bank_holder',
        'notes',
    ];

    protected $casts = [
        'issue_date'            => 'date',
        'due_date'              => 'date',
        'subtotal'              => 'float',
        'other_cost'            => 'float',
        'management_fee'        => 'float',
        'gross_total'           => 'float',
        'pph_percentage'        => 'float',
        'pph_amount'            => 'float',
        'net_amount'            => 'float',
        'include_pph_deduction' => 'boolean',
        'amount_paid'           => 'float',
        'remaining_balance'     => 'float',
    ];

    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $prefix = "TRV/INV/{$year}/";
        $latest = static::where('invoice_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($latest && preg_match('/(\d+)$/', $latest->invoice_number, $matches)) {
            $next = intval($matches[1]) + 1;
        } else {
            $next = 1;
        }

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(Offering::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProjectInvoiceItem::class)->orderBy('sort_order')->orderBy('id');
    }

    // Accessors
    public function getSubtotalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getOtherCostFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->other_cost, 0, ',', '.');
    }

    public function getManagementFeeFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->management_fee, 0, ',', '.');
    }

    public function getGrossTotalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->gross_total, 0, ',', '.');
    }

    public function getPphAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->pph_amount, 0, ',', '.');
    }

    public function getNetAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->net_amount, 0, ',', '.');
    }

    public function getAmountPaidFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->amount_paid, 0, ',', '.');
    }

    public function getRemainingBalanceFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->remaining_balance, 0, ',', '.');
    }

    public function getPaymentStatusBadgeAttribute(): array
    {
        return match($this->payment_status) {
            'unpaid'    => ['label' => 'Unpaid', 'class' => 'badge-danger', 'bg' => '#ef4444'],
            'partial'   => ['label' => 'Partial', 'class' => 'badge-warning', 'bg' => '#f59e0b'],
            'paid'      => ['label' => 'Paid', 'class' => 'badge-success', 'bg' => '#10b981'],
            'overdue'   => ['label' => 'Overdue', 'class' => 'badge-danger', 'bg' => '#dc2626'],
            'cancelled' => ['label' => 'Cancelled', 'class' => 'badge-muted', 'bg' => '#64748b'],
            default     => ['label' => ucfirst($this->payment_status), 'class' => 'badge-info', 'bg' => '#3b82f6'],
        };
    }
}
