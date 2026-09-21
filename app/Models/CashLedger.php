<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CashLedger extends Model
{
    protected $table = 'cash_ledger';

    protected $fillable = [
        'type', 'amount', 'note', 'source', 'booking_id', 'balance_after', 'created_by',
    ];

    protected $casts = [
        'amount'        => 'integer',
        'balance_after' => 'integer',
    ];

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // ─── Static Helpers ────────────────────────────────────────────────────────

    /**
     * Get current cash balance (last balance_after entry, or 0).
     */
    public static function currentBalance(): int
    {
        $last = static::latest()->first();
        return $last ? (int) $last->balance_after : 0;
    }

    /**
     * Add a manual cash transaction (in or out).
     * Automatically calculates and stores balance_after.
     */
    public static function addEntry(string $type, int $amount, string $note = '', string $source = 'manual', ?int $bookingId = null, ?string $createdBy = null): self
    {
        return DB::transaction(function () use ($type, $amount, $note, $source, $bookingId, $createdBy) {
            // Lock for concurrent safety
            $balance = static::lockForUpdate()->latest()->value('balance_after') ?? 0;

            $newBalance = $type === 'in'
                ? $balance + $amount
                : max(0, $balance - $amount);

            return static::create([
                'type'          => $type,
                'amount'        => $amount,
                'note'          => $note,
                'source'        => $source,
                'booking_id'    => $bookingId,
                'balance_after' => $newBalance,
                'created_by'    => $createdBy,
            ]);
        });
    }

    /**
     * Auto-add full net revenue from a confirmed booking into the cash ledger.
     * Net revenue = total_price - (hpp_price × participants)
     * This includes both travine.id margin AND partner commission portion,
     * since the ledger represents the full bank balance.
     * When a partner withdrawal is paid, it will be deducted separately.
     */
    public static function addFromBooking(Booking $booking): void
    {
        // Avoid duplicate entries for the same booking
        if (static::where('booking_id', $booking->id)->where('source', 'booking')->exists()) {
            return;
        }

        $tour      = $booking->tour;
        $hpp       = $tour ? (int) ($tour->hpp_price ?? 0) : 0;
        $netRevenue = max(0, (int) $booking->total_price - ($hpp * (int) $booking->participants));

        if ($netRevenue <= 0) {
            return; // Nothing to add if no net revenue
        }

        $commissionPart = $tour
            ? round(($netRevenue * ($tour->commission_percentage ?? 0)) / 100)
            : 0;

        $note = "Pendapatan bersih dari booking #{$booking->booking_code} ({$booking->name})"
            . " — Tour: {$tour?->title}"
            . ($commissionPart > 0 ? " | Incl. komisi partner: Rp " . number_format($commissionPart, 0, ',', '.') : '');

        static::addEntry(
            type:      'in',
            amount:    $netRevenue,
            note:      $note,
            source:    'booking',
            bookingId: $booking->id,
            createdBy: 'System (Auto)',
        );
    }

    /**
     * Sync historical confirmed bookings and paid withdrawals into cash ledger,
     * and recalculate balance_after for all entries chronologically.
     */
    public static function syncHistoricalData(): void
    {
        DB::transaction(function () {
            // 1. Sync confirmed bookings
            $confirmedBookings = Booking::with('tour')
                ->whereIn('booking_status', ['confirmed', 'departed', 'completed'])
                ->whereIn('payment_status', ['dp_paid', 'paid'])
                ->get();

            foreach ($confirmedBookings as $b) {
                $exists = static::where('booking_id', $b->id)
                    ->where('source', 'booking')
                    ->exists();

                if (!$exists) {
                    $tour = $b->tour;
                    $hpp = $tour ? (int) ($tour->hpp_price ?? 0) : 0;
                    $netRevenue = max(0, (int) $b->total_price - ($hpp * (int) $b->participants));

                    if ($netRevenue > 0) {
                        $commissionPart = $tour ? round(($netRevenue * ($tour->commission_percentage ?? 0)) / 100) : 0;
                        $note = "Pendapatan bersih dari booking #{$b->booking_code} ({$b->name})"
                            . " — Tour: " . ($tour?->title ?? '-')
                            . ($commissionPart > 0 ? " | Incl. komisi partner: Rp " . number_format($commissionPart, 0, ',', '.') : '');

                        $entry = new static([
                            'type'          => 'in',
                            'amount'        => $netRevenue,
                            'note'          => $note,
                            'source'        => 'booking',
                            'booking_id'    => $b->id,
                            'balance_after' => 0,
                            'created_by'    => 'System (Auto)',
                        ]);
                        $entry->timestamps = false;
                        $entry->created_at = $b->created_at;
                        $entry->updated_at = $b->created_at;
                        $entry->save();
                    }
                }
            }

            // 2. Sync paid withdrawals
            $paidWithdrawals = Withdrawal::with('partner')
                ->where('status', 'paid')
                ->get();

            foreach ($paidWithdrawals as $w) {
                $exists = static::where('source', 'withdrawal')
                    ->where('note', 'like', "%Withdrawal #{$w->id}%")
                    ->exists();

                if (!$exists) {
                    $partnerName = $w->partner?->company_name ?? $w->bank_account_name ?? 'Partner';
                    $note = "Penarikan komisi partner {$partnerName} (Withdrawal #{$w->id})";

                    $entry = new static([
                        'type'          => 'out',
                        'amount'        => (int) $w->amount,
                        'note'          => $note,
                        'source'        => 'withdrawal',
                        'booking_id'    => null,
                        'balance_after' => 0,
                        'created_by'    => 'Admin (System)',
                    ]);
                    $entry->timestamps = false;
                    $entry->created_at = $w->updated_at ?? $w->created_at;
                    $entry->updated_at = $w->updated_at ?? $w->created_at;
                    $entry->save();
                }
            }

            // 3. Recalculate running balance_after chronologically
            static::recalculateBalances();
        });
    }

    /**
     * Recalculate balance_after for all entries ordered chronologically by created_at and id.
     */
    public static function recalculateBalances(): void
    {
        $allEntries = static::orderBy('created_at', 'asc')->orderBy('id', 'asc')->get();
        $runningBalance = 0;

        foreach ($allEntries as $entry) {
            if ($entry->type === 'in') {
                $runningBalance += $entry->amount;
            } else {
                $runningBalance = max(0, $runningBalance - $entry->amount);
            }
            $entry->balance_after = $runningBalance;
            $entry->saveQuietly();
        }
    }

    // ─── Accessors ─────────────────────────────────────────────────────────────

    public function getAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getBalanceAfterFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->balance_after, 0, ',', '.');
    }
}
