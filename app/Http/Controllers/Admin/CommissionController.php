<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Commission::with(['partner', 'tour', 'booking']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('partner', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%")
                       ->orWhere('referral_code', 'like', "%{$search}%");
                })->orWhereHas('booking', function ($bq) use ($search) {
                    $bq->where('booking_code', 'like', "%{$search}%")
                       ->orWhere('name', 'like', "%{$search}%");
                });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($startDate = $request->input('start_date')) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate = $request->input('end_date')) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $commissions = $query->latest()->paginate(20)->withQueryString();

        $summary = [
            'total_amount' => Commission::whereNotIn('status', ['cancelled', 'rejected'])->sum('commission_amount'),
            'available'    => Commission::where('status', 'available')->sum('commission_amount'),
            'paid'         => Commission::where('status', 'paid')->sum('commission_amount'),
        ];

        return view('admin.commissions.index', compact('commissions', 'summary'));
    }

    public function updateStatus(Request $request, Commission $commission)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,available,cancelled',
        ]);

        $commission->update(['status' => $request->input('status')]);

        return back()->with('success', 'Status komisi berhasil diperbarui.');
    }
}
