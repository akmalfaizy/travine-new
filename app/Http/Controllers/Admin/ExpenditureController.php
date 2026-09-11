<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CashLedger;
use App\Models\Setting;
use App\Models\Tour;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ExpenditureController extends Controller
{
    private const PIN_SESSION_KEY = 'expenditure_pin_verified';
    private const PIN_SETTING_KEY = 'expenditure_pin';
    private const DEFAULT_PIN     = '123456';

    // ─── PIN Helpers ──────────────────────────────────────────────────────────

    private function isPinVerified(): bool
    {
        return session(self::PIN_SESSION_KEY) === true;
    }

    private function getStoredPin(): string
    {
        return Setting::where('key', self::PIN_SETTING_KEY)->value('value') ?? bcrypt(self::DEFAULT_PIN);
    }

    // ─── Main Page ────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $pinVerified = $this->isPinVerified();

        if (!$pinVerified) {
            return view('admin.expenditures.index', ['pinVerified' => false]);
        }

        // Auto-sync historical confirmed bookings & paid withdrawals into cash ledger
        CashLedger::syncHistoricalData();

        // ── Revenue Statistics ─────────────────────────────────────────────
        // Confirmed bookings (confirmed, departed, completed)
        $confirmedBookings = Booking::with('tour')
            ->whereIn('booking_status', ['confirmed', 'departed', 'completed'])
            ->whereIn('payment_status', ['dp_paid', 'paid'])
            ->get();

        // Pendapatan Kotor = total harga semua booking terkonfirmasi
        $grossRevenue = $confirmedBookings->sum('total_price');

        // Pendapatan Bersih = Kotor − HPP (modal) — sudah termasuk bagian komisi mitra
        $netRevenue = $confirmedBookings->sum(function ($b) {
            $hpp = $b->tour ? (int) ($b->tour->hpp_price ?? 0) : 0;
            return max(0, (int) $b->total_price - ($hpp * (int) $b->participants));
        });

        // Total komisi yang sudah ditransfer ke mitra (withdrawal paid)
        $totalCommission = Withdrawal::where('status', 'paid')->sum('amount');

        // Total Margin Travine = Pendapatan Bersih − komisi yang sudah dibayarkan ke mitra
        $totalMargin = max(0, $netRevenue - $totalCommission);

        // Cash balance (dari ledger — saldo bank sesungguhnya)
        $cashBalance = CashLedger::currentBalance();

        // ── Monthly Chart Data (last 12 months) ────────────────────────────
        $monthlyData = $this->getMonthlyData();

        // ── Ledger History ─────────────────────────────────────────────────
        $ledgerHistory = CashLedger::with('booking')
            ->latest()
            ->paginate(15);

        return view('admin.expenditures.index', compact(
            'pinVerified',
            'grossRevenue',
            'netRevenue',
            'totalCommission',
            'totalMargin',
            'cashBalance',
            'monthlyData',
            'ledgerHistory'
        ));
    }

    // ─── Monthly Data (JSON for Chart.js) ────────────────────────────────────

    public function monthlyData(): \Illuminate\Http\JsonResponse
    {
        if (!$this->isPinVerified()) {
            abort(403);
        }

        return response()->json($this->getMonthlyData());
    }

    private function getMonthlyData(): array
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $months[] = now()->subMonths($i)->format('Y-m');
        }

        $labels       = [];
        $gross        = [];
        $net          = [];
        $margin       = [];
        $bookingCount = [];

        foreach ($months as $month) {
            [$year, $m] = explode('-', $month);

            $monthBookings = Booking::with('tour')
                ->whereIn('booking_status', ['confirmed', 'departed', 'completed'])
                ->whereIn('payment_status', ['dp_paid', 'paid'])
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->get();

            $grossVal = $monthBookings->sum('total_price');
            $netVal   = $monthBookings->sum(function ($b) {
                $hpp = $b->tour ? (int) ($b->tour->hpp_price ?? 0) : 0;
                return max(0, (int) $b->total_price - ($hpp * (int) $b->participants));
            });

            $withdrawalVal = Withdrawal::whereYear('updated_at', $year)
                ->whereMonth('updated_at', $m)
                ->where('status', 'paid')
                ->sum('amount');

            $labels[]       = \Carbon\Carbon::createFromDate((int)$year, (int)$m, 1)->translatedFormat('M Y');
            $gross[]        = $grossVal;
            $net[]          = $netVal;
            $margin[]       = max(0, $netVal - $withdrawalVal);
            $bookingCount[] = $monthBookings->count();
        }

        return compact('labels', 'gross', 'net', 'margin', 'bookingCount');
    }

    // ─── PIN Verification ─────────────────────────────────────────────────────

    public function verifyPin(Request $request)
    {
        $request->validate(['pin' => 'required|string|max:10']);

        $stored = $this->getStoredPin();

        // Support both plain text (legacy) and hashed PINs
        $valid = Hash::check($request->pin, $stored)
            || $request->pin === $stored; // fallback plain

        if ($valid) {
            session([self::PIN_SESSION_KEY => true]);
            return redirect()->route('admin.expenditures.index')
                ->with('success', 'PIN benar. Selamat datang di menu Keuangan.');
        }

        return back()->withErrors(['pin' => 'PIN yang kamu masukkan salah.']);
    }

    // ─── Lock ─────────────────────────────────────────────────────────────────

    public function lockPin()
    {
        session()->forget(self::PIN_SESSION_KEY);
        return redirect()->route('admin.expenditures.index')
            ->with('success', 'Menu Keuangan telah dikunci.');
    }

    // ─── Store Ledger Entry ───────────────────────────────────────────────────

    public function storeLedger(Request $request)
    {
        if (!$this->isPinVerified()) {
            abort(403, 'PIN belum diverifikasi.');
        }

        $data = $request->validate([
            'type'   => 'required|in:in,out',
            'amount' => 'required|numeric|min:1',
            'note'   => 'required|string|max:500',
        ]);

        $admin = auth()->user()?->name ?? 'Admin';

        CashLedger::addEntry(
            type:      $data['type'],
            amount:    (int) $data['amount'],
            note:      $data['note'],
            source:    'manual',
            createdBy: $admin,
        );

        $label = $data['type'] === 'in' ? 'pemasukan' : 'pengeluaran';
        return back()->with('success', "Kas {$label} berhasil dicatat.");
    }

    // ─── Change PIN ───────────────────────────────────────────────────────────

    public function changePin(Request $request)
    {
        if (!$this->isPinVerified()) {
            abort(403, 'PIN belum diverifikasi.');
        }

        $data = $request->validate([
            'current_pin' => 'required|string',
            'new_pin'     => 'required|string|min:4|max:10|confirmed',
        ]);

        $stored = $this->getStoredPin();
        $valid  = Hash::check($data['current_pin'], $stored) || $data['current_pin'] === $stored;

        if (!$valid) {
            return back()->withErrors(['current_pin' => 'PIN saat ini tidak benar.']);
        }

        Setting::updateOrCreate(
            ['key' => self::PIN_SETTING_KEY],
            ['value' => bcrypt($data['new_pin'])]
        );

        return back()->with('success', 'PIN berhasil diubah.');
    }
}
