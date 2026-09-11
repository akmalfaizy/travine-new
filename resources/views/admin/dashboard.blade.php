@extends('admin.layout')
@section('title', 'Dashboard')
@section('content')
<div class="admin-topbar">
    <div>
        <h1>Dashboard</h1>
        <p style="color:var(--text-muted); font-size:0.85rem; margin-top:4px;">Selamat datang, {{ auth()->user()->name }}</p>
    </div>
    <div style="display:flex; gap:8px;">
        <a href="{{ route('admin.bookings.export') }}" class="admin-btn admin-btn--secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export CSV
        </a>
    </div>
</div>

{{-- Stats Grid --}}
<div class="admin-grid admin-grid--4" style="margin-bottom:28px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(20,184,166,0.15);">🌍</div>
        <div>
            <div class="stat-value">{{ $stats['total_tours'] }}</div>
            <div class="stat-label">Paket Wisata</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,0.15);">📋</div>
        <div>
            <div class="stat-value">{{ $stats['total_bookings'] }}</div>
            <div class="stat-label">Total Pemesanan</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(251,191,36,0.15);">⏳</div>
        <div>
            <div class="stat-value">{{ $stats['pending_bookings'] }}</div>
            <div class="stat-label">Perlu Verifikasi</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(168,85,247,0.15);">🤝</div>
        <div>
            <div class="stat-value">{{ $stats['total_partners'] }}</div>
            <div class="stat-label">Mitra Terdaftar</div>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 340px; gap:20px; align-items:start;">
    {{-- Recent Bookings --}}
    <div class="admin-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h2 style="font-size:1rem; font-weight:700;">Pemesanan Terbaru</h2>
            <a href="{{ route('admin.bookings.index') }}" class="admin-btn admin-btn--secondary admin-btn--sm">Lihat Semua</a>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Kode</th><th>Nama</th><th>Paket</th><th>Total</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
            @forelse($recent_bookings as $b)
                <tr>
                    <td><a href="{{ route('admin.bookings.show', $b) }}" style="color:var(--teal); font-weight:600; text-decoration:none;">{{ $b->booking_code }}</a></td>
                    <td>{{ $b->name }}</td>
                    <td style="color:var(--text-muted); font-size:0.85rem;">{{ Str::limit($b->tour?->title, 24) }}</td>
                    <td>{{ $b->total_price_formatted }}</td>
                    <td>
                        @php
                            $sc = match($b->booking_status) {
                                'confirmed','completed' => 'success',
                                'cancelled' => 'danger',
                                'waiting_verification','processing' => 'warning',
                                default => 'info',
                            };
                        @endphp
                        <span class="badge badge-{{ $sc }}">{{ $b->booking_status_label }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center; color:var(--text-muted); padding:32px;">Belum ada pemesanan</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Activity Log --}}
    <div class="admin-card">
        <h2 style="font-size:1rem; font-weight:700; margin-bottom:16px;">Aktivitas Terakhir</h2>
        <div style="display:flex; flex-direction:column; gap:10px;">
            @forelse($activity_logs as $log)
                <div style="display:flex; gap:10px; align-items:flex-start; padding-bottom:10px; border-bottom:1px solid var(--glass-border);">
                    <div style="width:28px; height:28px; border-radius:50%; background:rgba(20,184,166,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:0.7rem;">👤</div>
                    <div>
                        <div style="font-size:0.85rem; color:var(--text-primary);">{{ $log->description }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">{{ $log->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            @empty
                <p style="color:var(--text-muted); text-align:center; font-size:0.85rem; padding:20px 0;">Belum ada aktivitas</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
