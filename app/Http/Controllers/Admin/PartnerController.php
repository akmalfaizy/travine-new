<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $query = Partner::withCount(['bookings', 'clicks', 'commissions']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('referral_code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $partners = $query->latest()->paginate(15)->withQueryString();

        return view('admin.partners.index', compact('partners'));
    }

    public function show(Partner $partner)
    {
        $partner->load(['bookings.tour', 'commissions.tour', 'withdrawals']);

        $totalClicks          = $partner->clicks()->count();
        $totalBookings        = $partner->bookings()->count();
        $totalSales           = $partner->bookings()->whereIn('booking_status', ['processing', 'confirmed', 'departed', 'completed'])->sum('total_price');
        $availableCommission  = $partner->available_commission;
        $processingCommission = $partner->processing_commission;
        $paidCommission        = $partner->paid_commission;

        return view('admin.partners.show', compact(
            'partner', 'totalClicks', 'totalBookings', 'totalSales',
            'availableCommission', 'processingCommission', 'paidCommission'
        ));
    }

    public function toggleStatus(Request $request, Partner $partner)
    {
        $request->validate([
            'status' => 'required|in:active,suspended,inactive',
        ]);

        $newStatus = $request->input('status');
        $partner->update(['status' => $newStatus]);

        return back()->with('success', "Status mitra {$partner->name} berhasil diperbarui menjadi {$partner->status_label}.");
    }
}
