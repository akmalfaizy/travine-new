<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Mail\WithdrawalStatusMail;
use App\Models\Commission;
use App\Models\Partner;
use App\Models\Tour;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PartnerDashboardController extends Controller
{
    public function index()
    {
        /** @var Partner $partner */
        $partner = Auth::guard('partner')->user();

        $totalClicks        = $partner->clicks()->count();
        $totalBookings      = $partner->bookings()->count();
        $totalSales         = $partner->bookings()
                                ->whereNotIn('booking_status', ['cancelled'])
                                ->sum('total_price');

        $totalCommission     = $partner->total_commission;
        $availableCommission = $partner->available_commission;
        $processingCommission = $partner->processing_commission;
        $paidCommission       = $partner->paid_commission;

        $recentBookings    = $partner->bookings()->with('tour')->latest()->take(5)->get();
        $recentCommissions = $partner->commissions()->with(['tour', 'booking'])->latest()->take(5)->get();
        $recentWithdrawals = $partner->withdrawals()->latest()->take(5)->get();

        return view('partner.dashboard', compact(
            'partner', 'totalClicks', 'totalBookings', 'totalSales',
            'totalCommission', 'availableCommission', 'processingCommission',
            'paidCommission', 'recentBookings', 'recentCommissions', 'recentWithdrawals'
        ));
    }

    public function profile()
    {
        $partner = Auth::guard('partner')->user();
        return view('partner.profile', compact('partner'));
    }

    public function updateProfile(Request $request)
    {
        /** @var Partner $partner */
        $partner = Auth::guard('partner')->user();

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:20',
            'address'  => 'nullable|string|max:500',
            'city'     => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
        ]);

        $partner->update($data);

        return back()->with('success', 'Profil pribadi berhasil diperbarui.');
    }

    public function updateBankDetails(Request $request)
    {
        /** @var Partner $partner */
        $partner = Auth::guard('partner')->user();

        $data = $request->validate([
            'bank_name'           => 'required|string|max:100',
            'bank_account_name'   => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:50',
        ]);

        $partner->update($data);

        return back()->with('success', 'Informasi rekening bank pembayaran berhasil disimpan.');
    }

    public function referrals()
    {
        /** @var Partner $partner */
        $partner = Auth::guard('partner')->user();

        $tours = Tour::where('is_active', true)->with('category')->latest()->get();

        return view('partner.referrals', compact('partner', 'tours'));
    }

    public function orders()
    {
        /** @var Partner $partner */
        $partner = Auth::guard('partner')->user();

        $bookings = $partner->bookings()
            ->with(['tour', 'commission'])
            ->latest()
            ->paginate(15);

        return view('partner.orders', compact('partner', 'bookings'));
    }

    public function commissions()
    {
        /** @var Partner $partner */
        $partner = Auth::guard('partner')->user();

        $commissions = $partner->commissions()
            ->with(['tour', 'booking'])
            ->latest()
            ->paginate(15);

        return view('partner.commissions', compact('partner', 'commissions'));
    }

    public function withdrawals()
    {
        /** @var Partner $partner */
        $partner = Auth::guard('partner')->user();

        $withdrawals = $partner->withdrawals()
            ->latest()
            ->paginate(15);

        $minWithdrawal = 100000; // Rp 100.000

        return view('partner.withdrawals', compact('partner', 'withdrawals', 'minWithdrawal'));
    }

    public function storeWithdrawal(Request $request)
    {
        /** @var Partner $partner */
        $partner = Auth::guard('partner')->user();

        $minWithdrawal = 100000;

        if (!$partner->isProfileComplete()) {
            return redirect()->route('partner.profile')
                ->with('error', 'Silakan lengkapi informasi rekening bank Anda di halaman profil sebelum mengajukan penarikan komisi.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:' . $minWithdrawal,
        ], [
            'amount.min' => 'Nominal penarikan minimal adalah Rp ' . number_format($minWithdrawal, 0, ',', '.'),
        ]);

        $amount = (float) $request->input('amount');
        $available = $partner->available_commission;

        if ($amount > $available) {
            return back()->withErrors(['amount' => 'Nominal penarikan tidak boleh melebihi saldo komisi yang tersedia (Rp ' . number_format($available, 0, ',', '.') . ').']);
        }

        // Check if pending withdrawal exists
        $hasPending = $partner->withdrawals()
            ->whereIn('status', ['requested', 'approved', 'processing'])
            ->exists();

        if ($hasPending) {
            return back()->with('error', 'Anda masih memiliki pengajuan penarikan komisi yang sedang diproses. Mohon tunggu hingga proses selesai.');
        }

        DB::beginTransaction();
        try {
            $withdrawalCode = Withdrawal::generateCode();

            $withdrawal = Withdrawal::create([
                'partner_id'          => $partner->id,
                'withdrawal_code'     => $withdrawalCode,
                'amount'              => $amount,
                'bank_name'           => $partner->bank_name,
                'bank_account_name'   => $partner->bank_account_name,
                'bank_account_number' => $partner->bank_account_number,
                'status'              => 'requested',
            ]);

            DB::commit();

            // Send notification mail
            try {
                Mail::to($partner->email)->send(new WithdrawalStatusMail($withdrawal));
            } catch (\Throwable $e) {
                Log::error('Withdrawal notification mail failed: ' . $e->getMessage());
            }

            return redirect()->route('partner.withdrawals')
                ->with('success', 'Pengajuan penarikan komisi sebesar Rp ' . number_format($amount, 0, ',', '.') . ' berhasil dikirim! Tim finance kami akan meninjau pengajuan Anda.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Store withdrawal failed: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses pengajuan. Silakan coba lagi.');
        }
    }
}
