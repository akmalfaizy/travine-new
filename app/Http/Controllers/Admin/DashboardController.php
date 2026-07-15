<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Category;
use App\Models\News;
use App\Models\Tour;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tours'      => Tour::count(),
            'total_categories' => Category::count(),
            'total_bookings'   => Booking::count(),
            'pending_bookings' => Booking::where('booking_status', 'waiting_verification')->count(),
            'total_revenue'    => Booking::whereIn('payment_status', ['dp_paid', 'paid'])->sum('total_price'),
            'total_news'       => News::count(),
        ];

        $recent_bookings = Booking::with('tour')->latest()->take(5)->get();
        $activity_logs   = ActivityLog::with('user')->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recent_bookings', 'activity_logs'));
    }
}
