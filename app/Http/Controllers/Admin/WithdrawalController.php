<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WithdrawalStatusMail;
use App\Models\CashLedger;
use App\Models\Commission;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $query = Withdrawal::with('partner');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('withdrawal_code', 'like', "%{$search}%")
                  ->orWhere('bank_account_name', 'like', "%{$search}%")
                  ->orWhere('bank_account_number', 'like', "%{$search}%")
                  ->orWhereHas('partner', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $withdrawals = $query->latest()->paginate(15)->withQueryString();

        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    public function show(Withdrawal $withdrawal)
    {
        $withdrawal->load(['partner.commissions' => function ($q) {
            $q->latest()->take(10);
        }]);

        return view('admin.withdrawals.show', compact('withdrawal'));
    }

    public function updateStatus(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'status'           => 'required|in:approved,processing,paid,rejected',
            'rejection_reason' => 'nullable|required_if:status,rejected|string|max:1000',
            'transfer_proof'   => 'nullable|required_if:status,paid|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'admin_note'       => 'nullable|string|max:1000',
        ], [
            'rejection_reason.required_if' => 'Alasan penolakan wajib diisi jika pengajuan ditolak.',
            'transfer_proof.required_if'   => 'Bukti transfer wajib diunggah untuk status Dibayar.',
        ]);

        $newStatus = $request->input('status');

        DB::beginTransaction();
        try {
            if ($newStatus === 'paid') {
                if ($request->hasFile('transfer_proof')) {
                    $proofPath = $request->file('transfer_proof')->store('proofs/withdrawals', 'public');
                    $withdrawal->transfer_proof = $proofPath;
                }
                $withdrawal->paid_at    = now();
                $withdrawal->status     = 'paid';
                $withdrawal->admin_note = $request->input('admin_note');

                // Auto-deduct from cash ledger (partner commission paid out)
                try {
                    $withdrawal->load('partner');
                    $partnerName = $withdrawal->partner?->name ?? 'Partner';
                    CashLedger::addEntry(
                        type:      'out',
                        amount:    (int) $withdrawal->amount,
                        note:      "Transfer komisi partner: {$partnerName} — Kode Penarikan: {$withdrawal->withdrawal_code} | Rek: {$withdrawal->bank_name} a.n. {$withdrawal->bank_account_name} ({$withdrawal->bank_account_number})",
                        source:    'withdrawal',
                        createdBy: 'System (Withdrawal Auto)',
                    );
                } catch (\Throwable $e) {
                    Log::error('Failed to deduct cash ledger for withdrawal #' . $withdrawal->id . ': ' . $e->getMessage());
                }
            } elseif ($newStatus === 'rejected') {
                $withdrawal->status           = 'rejected';
                $withdrawal->rejection_reason = $request->input('rejection_reason');
                $withdrawal->admin_note       = $request->input('admin_note');
            } else {
                $withdrawal->status     = $newStatus;
                $withdrawal->admin_note = $request->input('admin_note');
            }

            $withdrawal->save();
            DB::commit();

            // Send notification mail to partner
            try {
                Mail::to($withdrawal->partner->email)->send(new WithdrawalStatusMail($withdrawal));
            } catch (\Throwable $e) {
                Log::error('Withdrawal status update email failed: ' . $e->getMessage());
            }

            return redirect()->route('admin.withdrawals.index')
                ->with('success', "Status penarikan #{$withdrawal->withdrawal_code} berhasil diperbarui menjadi {$withdrawal->status_label}.");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Update withdrawal status failed: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
