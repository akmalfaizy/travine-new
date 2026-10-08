<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisitorLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InsightController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->input('range', '7days');
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');

        $now = Carbon::now();

        switch ($range) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate   = $now->copy()->endOfDay();
                $periodLabel = 'Hari Ini (' . $now->translatedFormat('d M Y') . ')';
                break;
            case 'yesterday':
                $startDate = $now->copy()->subDay()->startOfDay();
                $endDate   = $now->copy()->subDay()->endOfDay();
                $periodLabel = 'Kemarin (' . $now->copy()->subDay()->translatedFormat('d M Y') . ')';
                break;
            case '30days':
                $startDate = $now->copy()->subDays(29)->startOfDay();
                $endDate   = $now->copy()->endOfDay();
                $periodLabel = '30 Hari Terakhir';
                break;
            case 'this_month':
                $startDate = $now->copy()->startOfMonth();
                $endDate   = $now->copy()->endOfMonth();
                $periodLabel = 'Bulan Ini (' . $now->translatedFormat('F Y') . ')';
                break;
            case 'custom':
                $startDate = $customStart ? Carbon::parse($customStart)->startOfDay() : $now->copy()->subDays(6)->startOfDay();
                $endDate   = $customEnd ? Carbon::parse($customEnd)->endOfDay() : $now->copy()->endOfDay();
                $periodLabel = $startDate->translatedFormat('d M Y') . ' — ' . $endDate->translatedFormat('d M Y');
                break;
            case '7days':
            default:
                $range = '7days';
                $startDate = $now->copy()->subDays(6)->startOfDay();
                $endDate   = $now->copy()->endOfDay();
                $periodLabel = '7 Hari Terakhir';
                break;
        }

        // Base Query
        $baseQuery = VisitorLog::whereBetween('visited_at', [$startDate, $endDate])
            ->where('is_bot', false);

        // 1. Stats Summary
        $totalPageViews = (clone $baseQuery)->count();
        $uniqueVisitors = (clone $baseQuery)->distinct('ip_address')->count('ip_address');
        $todayVisitors  = VisitorLog::whereDate('visited_at', Carbon::today())->where('is_bot', false)->count();

        $topCityRecord = (clone $baseQuery)
            ->whereNotNull('city')
            ->select('city', 'province', DB::raw('count(*) as total'))
            ->groupBy('city', 'province')
            ->orderByDesc('total')
            ->first();

        // 2. Traffic Trend Chart Data
        $trendData = $this->buildTrafficTrend($startDate, $endDate, $range);

        // 3. Top Cities & Regencies (Kota & Kabupaten)
        $topCities = (clone $baseQuery)
            ->whereNotNull('city')
            ->select('city', 'province', DB::raw('count(*) as total'))
            ->groupBy('city', 'province')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // 4. Top Provinces
        $topProvinces = (clone $baseQuery)
            ->whereNotNull('province')
            ->select('province', DB::raw('count(*) as total'))
            ->groupBy('province')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        // 5. Device Breakdown (Desktop vs Mobile vs Tablet)
        $devices = (clone $baseQuery)
            ->select('device_type', DB::raw('count(*) as total'))
            ->groupBy('device_type')
            ->get();

        // 6. Browser Breakdown
        $browsers = (clone $baseQuery)
            ->whereNotNull('browser')
            ->select('browser', DB::raw('count(*) as total'))
            ->groupBy('browser')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // 7. Top Visited Pages
        $topPages = (clone $baseQuery)
            ->select('path', DB::raw('count(*) as total'))
            ->groupBy('path')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // 8. Recent Visitor Logs (Latest 20)
        $recentVisitors = (clone $baseQuery)
            ->latest('visited_at')
            ->take(20)
            ->get();

        return view('admin.insights.index', compact(
            'range',
            'startDate',
            'endDate',
            'periodLabel',
            'totalPageViews',
            'uniqueVisitors',
            'todayVisitors',
            'topCityRecord',
            'trendData',
            'topCities',
            'topProvinces',
            'devices',
            'browsers',
            'topPages',
            'recentVisitors'
        ));
    }

    /**
     * Build day-by-day or hour-by-hour traffic trend for Chart.js
     */
    protected function buildTrafficTrend(Carbon $start, Carbon $end, string $range): array
    {
        $labels = [];
        $pageViews = [];
        $uniqueVisitors = [];

        if ($range === 'today' || $range === 'yesterday') {
            // Group by hour
            for ($h = 0; $h < 24; $h++) {
                $hourStart = $start->copy()->setHour($h)->setMinute(0)->setSecond(0);
                $hourEnd   = $start->copy()->setHour($h)->setMinute(59)->setSecond(59);

                $labels[] = sprintf('%02d:00', $h);
                $pv = VisitorLog::whereBetween('visited_at', [$hourStart, $hourEnd])->where('is_bot', false)->count();
                $uv = VisitorLog::whereBetween('visited_at', [$hourStart, $hourEnd])->where('is_bot', false)->distinct('ip_address')->count('ip_address');

                $pageViews[] = $pv;
                $uniqueVisitors[] = $uv;
            }
        } else {
            // Group by day
            $period = Carbon::parse($start)->daysUntil($end);
            foreach ($period as $date) {
                $dayStart = $date->copy()->startOfDay();
                $dayEnd   = $date->copy()->endOfDay();

                $labels[] = $date->translatedFormat('d M');
                $pv = VisitorLog::whereBetween('visited_at', [$dayStart, $dayEnd])->where('is_bot', false)->count();
                $uv = VisitorLog::whereBetween('visited_at', [$dayStart, $dayEnd])->where('is_bot', false)->distinct('ip_address')->count('ip_address');

                $pageViews[] = $pv;
                $uniqueVisitors[] = $uv;
            }
        }

        return [
            'labels'         => $labels,
            'pageViews'      => $pageViews,
            'uniqueVisitors' => $uniqueVisitors,
        ];
    }
}
