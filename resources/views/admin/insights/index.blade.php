@extends('admin.layout')

@section('title', 'Insight & Analitik Pengunjung')

@push('styles')
<style>
    .insight-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
    }
    .insight-title-group h1 {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 4px;
    }
    .insight-subtitle {
        color: var(--text-muted);
        font-size: 0.85rem;
    }

    /* Filter Bar */
    .insight-filter-bar {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        padding: 8px 12px;
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .insight-pill {
        padding: 6px 14px;
        border-radius: var(--radius-sm);
        font-size: 0.825rem;
        font-weight: 600;
        text-decoration: none;
        color: var(--text-secondary);
        transition: var(--transition);
    }
    .insight-pill:hover {
        background: rgba(255,255,255,0.06);
        color: var(--text-primary);
    }
    .insight-pill.active {
        background: var(--teal);
        color: #ffffff;
        box-shadow: 0 2px 10px rgba(20,184,166,0.3);
    }

    /* Summary Stat Cards */
    .insight-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }
    .insight-stat-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-lg);
        padding: 22px 24px;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .insight-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.25);
    }
    .insight-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
    }
    .insight-stat-card--teal::before { background: linear-gradient(90deg, #14b8a6, #06b6d4); }
    .insight-stat-card--blue::before { background: linear-gradient(90deg, #3b82f6, #6366f1); }
    .insight-stat-card--amber::before { background: linear-gradient(90deg, #f59e0b, #ef4444); }
    .insight-stat-card--purple::before { background: linear-gradient(90deg, #a855f7, #ec4899); }

    .insight-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        margin-bottom: 12px;
    }
    .insight-stat-icon--teal { background: rgba(20,184,166,0.15); color: #14b8a6; }
    .insight-stat-icon--blue { background: rgba(59,130,246,0.15); color: #3b82f6; }
    .insight-stat-icon--amber { background: rgba(245,158,11,0.15); color: #f59e0b; }
    .insight-stat-icon--purple { background: rgba(168,85,247,0.15); color: #a855f7; }

    .insight-stat-value {
        font-size: 1.75rem;
        font-weight: 850;
        color: var(--text-primary);
        line-height: 1.1;
    }
    .insight-stat-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-top: 6px;
    }
    .insight-stat-sub {
        font-size: 0.75rem;
        color: var(--teal);
        margin-top: 4px;
        display: block;
    }

    /* Charts Container */
    .insight-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-lg);
        padding: 24px;
        margin-bottom: 24px;
    }
    .insight-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .insight-card-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Progress bar items */
    .insight-bar-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .insight-bar-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .insight-bar-label-group {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
    }
    .insight-bar-name {
        font-weight: 600;
        color: var(--text-primary);
    }
    .insight-bar-sub {
        color: var(--text-muted);
        font-size: 0.78rem;
        margin-left: 6px;
    }
    .insight-bar-count {
        font-weight: 700;
        color: var(--teal);
    }
    .insight-progress-bg {
        width: 100%;
        height: 7px;
        background: rgba(255,255,255,0.06);
        border-radius: 999px;
        overflow: hidden;
    }
    .insight-progress-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, var(--teal), #06b6d4);
        transition: width 0.6s ease;
    }

    @media (max-width: 1100px) {
        .insight-stat-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 650px) {
        .insight-stat-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="insight-header">
    <div class="insight-title-group">
        <h1>
            <span>📊</span> Insight & Analitik Pengunjung
        </h1>
        <div class="insight-subtitle">
            Statistik jumlah pengunjung web, persebaran kota/kabupaten asal, perangkat, dan halaman favorit.
        </div>
    </div>

    {{-- Filter Range --}}
    <div class="insight-filter-bar">
        <a href="{{ route('admin.insights.index', ['range' => 'today']) }}" class="insight-pill {{ $range === 'today' ? 'active' : '' }}">Hari Ini</a>
        <a href="{{ route('admin.insights.index', ['range' => 'yesterday']) }}" class="insight-pill {{ $range === 'yesterday' ? 'active' : '' }}">Kemarin</a>
        <a href="{{ route('admin.insights.index', ['range' => '7days']) }}" class="insight-pill {{ $range === '7days' ? 'active' : '' }}">7 Hari</a>
        <a href="{{ route('admin.insights.index', ['range' => '30days']) }}" class="insight-pill {{ $range === '30days' ? 'active' : '' }}">30 Hari</a>
        <a href="{{ route('admin.insights.index', ['range' => 'this_month']) }}" class="insight-pill {{ $range === 'this_month' ? 'active' : '' }}">Bulan Ini</a>
    </div>
</div>

{{-- 4 Highlight Stat Cards --}}
<div class="insight-stat-grid">
    {{-- Card 1: Page Views --}}
    <div class="insight-stat-card insight-stat-card--teal">
        <div class="insight-stat-icon insight-stat-icon--teal">👁️</div>
        <div class="insight-stat-value">{{ number_format($totalPageViews) }}</div>
        <div class="insight-stat-label">Total Kunjungan Halaman</div>
        <span class="insight-stat-sub">Periode: {{ $periodLabel }}</span>
    </div>

    {{-- Card 2: Unique Visitors --}}
    <div class="insight-stat-card insight-stat-card--blue">
        <div class="insight-stat-icon insight-stat-icon--blue">👥</div>
        <div class="insight-stat-value">{{ number_format($uniqueVisitors) }}</div>
        <div class="insight-stat-label">Pengunjung Unik (Orang)</div>
        <span class="insight-stat-sub">Dihitung per IP / Sesi unik</span>
    </div>

    {{-- Card 3: Top City --}}
    <div class="insight-stat-card insight-stat-card--amber">
        <div class="insight-stat-icon insight-stat-icon--amber">📍</div>
        <div class="insight-stat-value" style="font-size:1.35rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $topCityRecord->city ?? 'Belum ada data' }}">
            {{ $topCityRecord->city ?? '-' }}
        </div>
        <div class="insight-stat-label">Kota / Asal Terbanyak</div>
        <span class="insight-stat-sub">{{ $topCityRecord ? ($topCityRecord->province . ' (' . $topCityRecord->total . ' kunjungan)') : '-' }}</span>
    </div>

    {{-- Card 4: Today's visits --}}
    <div class="insight-stat-card insight-stat-card--purple">
        <div class="insight-stat-icon insight-stat-icon--purple">⚡</div>
        <div class="insight-stat-value">{{ number_format($todayVisitors) }}</div>
        <div class="insight-stat-label">Kunjungan Hari Ini</div>
        <span class="insight-stat-sub">Real-time update</span>
    </div>
</div>

{{-- Main Traffic Trend Line Chart --}}
<div class="insight-card">
    <div class="insight-card-header">
        <div class="insight-card-title">
            <span>📈</span> Grafik Tren Kunjungan Web
        </div>
        <span style="font-size:0.8rem; color:var(--text-muted);">{{ $periodLabel }}</span>
    </div>
    <div style="height: 320px; position: relative;">
        <canvas id="trafficTrendChart"></canvas>
    </div>
</div>

{{-- Two-Column Grid: Kota/Kabupaten & Provinsi --}}
<div class="admin-grid admin-grid--2" style="margin-bottom: 24px;">
    {{-- Left: Top Cities / Regencies --}}
    <div class="insight-card" style="margin-bottom: 0;">
        <div class="insight-card-header">
            <div class="insight-card-title">
                <span>🏙️</span> Top 10 Kota & Kabupaten Pengunjung
            </div>
            <span style="font-size:0.78rem; color:var(--teal); font-weight:700;">Berdasarkan IP Geolocation</span>
        </div>

        @if($topCities->count() > 0)
            @php $maxCityVisits = $topCities->max('total') ?: 1; @endphp
            <div class="insight-bar-list">
                @foreach($topCities as $index => $city)
                    @php $pct = round(($city->total / $totalPageViews) * 100, 1); @endphp
                    <div class="insight-bar-item">
                        <div class="insight-bar-label-group">
                            <div>
                                <span style="font-weight:700; color:var(--text-muted); margin-right:4px;">#{{ $index + 1 }}</span>
                                <span class="insight-bar-name">{{ $city->city ?: 'Unknown' }}</span>
                                <span class="insight-bar-sub">({{ $city->province ?: 'Indonesia' }})</span>
                            </div>
                            <div>
                                <span class="insight-bar-count">{{ number_format($city->total) }}</span>
                                <span style="font-size:0.75rem; color:var(--text-muted); margin-left:4px;">({{ $pct }}%)</span>
                            </div>
                        </div>
                        <div class="insight-progress-bg">
                            <div class="insight-progress-fill" style="width: {{ ($city->total / $maxCityVisits) * 100 }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align:center; padding:40px; color:var(--text-muted);">
                Belum ada data kunjungan kota.
            </div>
        @endif
    </div>

    {{-- Right: Top Provinces --}}
    <div class="insight-card" style="margin-bottom: 0;">
        <div class="insight-card-header">
            <div class="insight-card-title">
                <span>🗺️</span> Persebaran Provinsi
            </div>
            <span style="font-size:0.78rem; color:var(--text-muted);">Wilayah Indonesia</span>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; align-items:center;">
            <div style="height: 230px; position:relative;">
                <canvas id="provinceChart"></canvas>
            </div>
            <div style="display:flex; flex-direction:column; gap:8px;">
                @foreach($topProvinces->take(6) as $prov)
                    @php $pPct = round(($prov->total / ($totalPageViews ?: 1)) * 100, 1); @endphp
                    <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.83rem;">
                        <span style="color:var(--text-secondary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:140px;" title="{{ $prov->province }}">
                            {{ $prov->province ?: 'Lainnya' }}
                        </span>
                        <span style="font-weight:700; color:var(--text-primary);">
                            {{ number_format($prov->total) }} <small style="color:var(--text-muted); font-weight:normal;">({{ $pPct }}%)</small>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Two-Column Grid: Device/Browser & Top Visited Pages --}}
<div class="admin-grid admin-grid--2" style="margin-bottom: 24px;">
    {{-- Device & Browser Breakdown --}}
    <div class="insight-card" style="margin-bottom: 0;">
        <div class="insight-card-header">
            <div class="insight-card-title">
                <span>📱</span> Perangkat & Browser
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; align-items:center;">
            <div style="height: 220px; position:relative;">
                <canvas id="deviceChart"></canvas>
            </div>
            <div>
                <h4 style="font-size:0.85rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-bottom:12px; letter-spacing:0.05em;">Browser Terpopuler</h4>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    @foreach($browsers as $b)
                        @php $bPct = round(($b->total / ($totalPageViews ?: 1)) * 100, 1); @endphp
                        <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.83rem;">
                            <span style="color:var(--text-secondary);">🌐 {{ $b->browser }}</span>
                            <span style="font-weight:700; color:var(--teal);">{{ number_format($b->total) }} <small style="color:var(--text-muted);">({{ $bPct }}%)</small></span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Top Visited Pages --}}
    <div class="insight-card" style="margin-bottom: 0;">
        <div class="insight-card-header">
            <div class="insight-card-title">
                <span>📄</span> Halaman Paling Sering Dibuka
            </div>
            <span style="font-size:0.78rem; color:var(--text-muted);">Top 10 Halaman</span>
        </div>

        <table class="admin-table" style="font-size:0.85rem;">
            <thead>
                <tr>
                    <th>URL Path</th>
                    <th style="text-align:right;">Views</th>
                    <th style="text-align:right;">% Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topPages as $page)
                    @php $pagePct = round(($page->total / ($totalPageViews ?: 1)) * 100, 1); @endphp
                    <tr>
                        <td>
                            <code style="background:rgba(20,184,166,0.1); color:var(--teal); padding:2px 8px; border-radius:4px; font-weight:600;">
                                {{ $page->path }}
                            </code>
                        </td>
                        <td style="text-align:right; font-weight:700; color:var(--text-primary);">
                            {{ number_format($page->total) }}
                        </td>
                        <td style="text-align:right; color:var(--text-muted);">
                            {{ $pagePct }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align:center; color:var(--text-muted);">Belum ada data halaman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Real-time Visitor Activity Table --}}
<div class="insight-card">
    <div class="insight-card-header">
        <div class="insight-card-title">
            <span>⚡</span> Log Aktivitas Pengunjung Terbaru (20 Terakhir)
        </div>
        <span style="font-size:0.8rem; color:var(--teal); font-weight:700;">🟢 Live Real-time</span>
    </div>

    <div style="overflow-x:auto;">
        <table class="admin-table" style="font-size:0.85rem;">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Asal Kota / Wilayah</th>
                    <th>Halaman (Path)</th>
                    <th>Perangkat & Browser</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentVisitors as $v)
                    <tr>
                        <td style="white-space:nowrap; color:var(--text-muted);">
                            {{ $v->visited_at->diffForHumans() }}
                            <small style="display:block; font-size:0.75rem; color:var(--text-muted);">{{ $v->visited_at->format('d M, H:i') }}</small>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <span style="font-size:1rem;">🇮🇩</span>
                                <div>
                                    <strong style="color:var(--text-primary); display:block;">{{ $v->city ?: 'Indonesia' }}</strong>
                                    <small style="color:var(--text-muted);">{{ $v->province ?: '-' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="{{ $v->path }}" target="_blank" style="color:var(--teal); text-decoration:none; font-family:monospace; font-weight:600;">
                                {{ $v->path }}
                            </a>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <span>{{ $v->device_type === 'mobile' ? '📱' : ($v->device_type === 'tablet' ? '📟' : '💻') }}</span>
                                <span>{{ ucfirst($v->device_type) }} &bull; {{ $v->browser }} ({{ $v->platform }})</span>
                            </div>
                        </td>
                        <td>
                            <code style="background:rgba(255,255,255,0.05); padding:2px 6px; border-radius:4px; font-size:0.78rem; color:var(--text-muted);">
                                {{ $v->ip_address }}
                            </code>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:30px; color:var(--text-muted);">
                            Belum ada catatan pengunjung.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
{{-- Load Chart.js from CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Chart Defaults for dark aesthetic
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.font.family = "'Inter', sans-serif";

    // 1. Traffic Trend Line Chart
    const trendCtx = document.getElementById('trafficTrendChart');
    if (trendCtx) {
        const trendLabels = @json($trendData['labels']);
        const pvData = @json($trendData['pageViews']);
        const uvData = @json($trendData['uniqueVisitors']);

        const gradientTeal = trendCtx.getContext('2d').createLinearGradient(0, 0, 0, 300);
        gradientTeal.addColorStop(0, 'rgba(20, 184, 166, 0.45)');
        gradientTeal.addColorStop(1, 'rgba(20, 184, 166, 0.0)');

        const gradientBlue = trendCtx.getContext('2d').createLinearGradient(0, 0, 0, 300);
        gradientBlue.addColorStop(0, 'rgba(59, 130, 246, 0.35)');
        gradientBlue.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [
                    {
                        label: 'Total Kunjungan (Page Views)',
                        data: pvData,
                        borderColor: '#14b8a6',
                        backgroundColor: gradientTeal,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#14b8a6',
                        pointBorderColor: '#ffffff',
                        pointHoverRadius: 6,
                        borderWidth: 2.5
                    },
                    {
                        label: 'Pengunjung Unik (Orang)',
                        data: uvData,
                        borderColor: '#3b82f6',
                        backgroundColor: gradientBlue,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#ffffff',
                        pointHoverRadius: 6,
                        borderWidth: 2.5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            font: { size: 12, weight: 600 }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        titleColor: '#ffffff',
                        bodyColor: '#e2e8f0',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 12,
                        boxPadding: 6,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255, 255, 255, 0.04)' },
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.04)' },
                        ticks: { precision: 0, font: { size: 11 } }
                    }
                }
            }
        });
    }

    // 2. Province Doughnut Chart
    const provCtx = document.getElementById('provinceChart');
    if (provCtx) {
        const provData = @json($topProvinces);
        const provLabels = provData.map(p => p.province || 'Lainnya');
        const provCounts = provData.map(p => p.total);

        new Chart(provCtx, {
            type: 'doughnut',
            data: {
                labels: provLabels,
                datasets: [{
                    data: provCounts,
                    backgroundColor: [
                        '#14b8a6', '#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#64748b'
                    ],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        padding: 10,
                        cornerRadius: 8
                    }
                },
                cutout: '70%'
            }
        });
    }

    // 3. Device Doughnut Chart
    const devCtx = document.getElementById('deviceChart');
    if (devCtx) {
        const devData = @json($devices);
        const devLabels = devData.map(d => d.device_type.toUpperCase());
        const devCounts = devData.map(d => d.total);

        new Chart(devCtx, {
            type: 'doughnut',
            data: {
                labels: devLabels,
                datasets: [{
                    data: devCounts,
                    backgroundColor: ['#14b8a6', '#3b82f6', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            font: { size: 11, weight: 600 }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        padding: 10,
                        cornerRadius: 8
                    }
                },
                cutout: '68%'
            }
        });
    }
});
</script>
@endpush
