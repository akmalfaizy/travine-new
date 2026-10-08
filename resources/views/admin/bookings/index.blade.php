@extends('admin.layout')
@section('title', 'Manajemen Pemesanan & Manifest Open Trip')
@section('content')
<div class="admin-topbar">
    <div>
        <h1>Manajemen Pemesanan</h1>
        <p style="color:var(--text-muted); font-size:0.85rem; margin-top:2px;">
            Pantau seluruh data pesanan, kelola status peserta, dan ekspor manifest untuk operasional lapangan.
        </p>
    </div>
    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
        <a href="{{ route('admin.bookings.create') }}" class="admin-btn admin-btn--primary">
            + Buat Booking
        </a>
        <a href="{{ route('admin.bookings.export-manifest-pdf', request()->query()) }}"
           class="admin-btn admin-btn--secondary"
           style="border-color:rgba(239,68,68,0.4); color:#f87171; display:inline-flex; align-items:center; gap:6px; font-weight:700;"
           title="Export Manifest Data Peserta Open Trip ke format PDF (Sesuai filter)">
            📄 Export PDF Manifest
        </a>
        <a href="{{ route('admin.bookings.export', request()->query()) }}" class="admin-btn admin-btn--secondary">
            ↓ Export CSV
        </a>
        <a href="{{ route('admin.bookings.export-insurance', request()->query()) }}"
           class="admin-btn admin-btn--secondary"
           style="border-color:rgba(251,191,36,0.3);color:#fbbf24;"
           title="Export data peserta untuk keperluan asuransi (satu baris per peserta)">
            🛡 CSV Asuransi
        </a>
    </div>
</div>

{{-- Filter Card --}}
<div class="admin-card" style="padding:18px 20px; margin-bottom:16px;">
    <form method="GET" style="display:flex; flex-direction:column; gap:14px;">
        <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
            {{-- Filter Paket Wisata / Open Trip --}}
            <div style="flex: 1; min-width: 220px;">
                <label class="form-label" style="font-size:0.78rem; font-weight:700; color:var(--text-secondary);">
                    🎒 Paket Wisata / Open Trip
                </label>
                <select name="tour_id" class="form-control" style="font-size:0.85rem;">
                    <option value="">Semua Paket Wisata</option>
                    @if(isset($tours))
                        @foreach($tours as $t)
                            <option value="{{ $t->id }}" {{ request('tour_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->title }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            {{-- Filter Tanggal Keberangkatan --}}
            <div style="width: 170px;">
                <label class="form-label" style="font-size:0.78rem; font-weight:700; color:var(--text-secondary);">
                    📅 Tgl Keberangkatan
                </label>
                <input name="departure_date" type="date" class="form-control" value="{{ request('departure_date') }}" style="font-size:0.85rem;">
            </div>

            {{-- Filter Status Booking --}}
            <div style="width: 160px;">
                <label class="form-label" style="font-size:0.78rem; font-weight:700; color:var(--text-secondary);">
                    Status Booking
                </label>
                <select name="status" class="form-control" style="font-size:0.85rem;">
                    <option value="">Semua Status</option>
                    @foreach(['waiting_payment'=>'Menunggu Bayar','waiting_verification'=>'Verifikasi','processing'=>'Diproses','confirmed'=>'Dikonfirmasi','departed'=>'Berangkat','completed'=>'Selesai','cancelled'=>'Dibatalkan'] as $v=>$l)
                        <option value="{{ $v }}" {{ request('status') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Status Pembayaran --}}
            <div style="width: 150px;">
                <label class="form-label" style="font-size:0.78rem; font-weight:700; color:var(--text-secondary);">
                    Status Bayar
                </label>
                <select name="payment" class="form-control" style="font-size:0.85rem;">
                    <option value="">Semua Bayar</option>
                    @foreach(['unpaid'=>'Belum Bayar','dp_paid'=>'DP Dibayar','paid'=>'Lunas','refund'=>'Refund','failed'=>'Gagal'] as $v=>$l)
                        <option value="{{ $v }}" {{ request('payment') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Pencarian Kata Kunci --}}
            <div style="width: 180px;">
                <label class="form-label" style="font-size:0.78rem; font-weight:700; color:var(--text-secondary);">
                    Cari (Kode/Nama/HP)
                </label>
                <input name="search" type="text" class="form-control" value="{{ request('search') }}" placeholder="Ketik kata kunci..." style="font-size:0.85rem;">
            </div>

            {{-- Tombol Aksi Filter --}}
            <div style="display:flex; gap:8px;">
                <button type="submit" class="admin-btn admin-btn--primary" style="padding:10px 18px;">
                    🔍 Filter
                </button>
                <a href="{{ route('admin.bookings.index') }}" class="admin-btn admin-btn--secondary" style="padding:10px 14px;" title="Reset Semua Filter">
                    ↺ Reset
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Banner Ringkasan Filter & Quick Action Export PDF --}}
@if(request('tour_id') || request('departure_date') || request('status') || request('payment') || request('search'))
<div style="background:rgba(20,184,166,0.08); border:1px solid rgba(20,184,166,0.3); border-radius:10px; padding:12px 18px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        <span style="background:var(--teal); color:#ffffff; font-size:0.75rem; font-weight:800; padding:2px 8px; border-radius:4px; text-transform:uppercase;">
            Filter Aktif
        </span>
        <strong style="color:var(--text-primary); font-size:0.95rem;">
            {{ isset($selectedTour) && $selectedTour ? $selectedTour->title : 'Semua Paket' }}
        </strong>
        @if(request('departure_date'))
            &bull; Keberangkatan: <strong style="color:var(--teal);">{{ \Carbon\Carbon::parse(request('departure_date'))->translatedFormat('d M Y') }}</strong>
        @endif
        <span style="color:var(--text-muted); font-size:0.85rem;">
            (Hasil: <strong>{{ $bookings->total() }}</strong> Booking &bull; <strong>{{ $totalPax ?? $bookings->sum('participants') }}</strong> Orang Peserta / Pax)
        </span>
    </div>

    <a href="{{ route('admin.bookings.export-manifest-pdf', request()->query()) }}"
       class="admin-btn admin-btn--primary admin-btn--sm"
       style="display:inline-flex; align-items:center; gap:6px; font-weight:700;">
        📄 Unduh PDF Manifest Tim Lapangan
    </a>
</div>
@endif

<div class="admin-card" style="padding:0; overflow:hidden;">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="width:90px;">Kode</th>
                <th>Pemesan (PIC)</th>
                <th>Paket Wisata</th>
                <th style="width:120px;">Tgl Berangkat</th>
                <th style="width:70px; text-align:center;">Peserta</th>
                <th style="width:110px;">Total</th>
                <th style="width:120px;">Status Booking</th>
                <th style="width:110px;">Status Bayar</th>
                <th style="width:130px; text-align:right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($bookings as $b)
            <tr>
                <td>
                    <a href="{{ route('admin.bookings.show', $b) }}" style="color:var(--teal); font-weight:700; text-decoration:none; font-family:monospace; font-size:0.9rem;">
                        {{ $b->booking_code }}
                    </a>
                </td>
                <td>
                    <div style="font-weight:600; color:var(--text-primary);">{{ $b->name }}</div>
                    <div style="font-size:0.78rem; color:var(--text-muted);">📱 {{ $b->whatsapp }}</div>
                    @if($b->notes)
                        <div style="font-size:0.72rem; color:#f59e0b; margin-top:2px;" title="{{ $b->notes }}">
                            📝 {{ Str::limit($b->notes, 28) }}
                        </div>
                    @endif
                </td>
                <td style="font-size:0.85rem; color:var(--text-secondary);">
                    <div style="font-weight:600;">{{ Str::limit($b->tour?->title ?? '-', 32) }}</div>
                    @if($b->tour?->location)
                        <small style="color:var(--text-muted); font-size:0.75rem;">📍 {{ $b->tour->location }}</small>
                    @endif
                </td>
                <td style="font-size:0.85rem;">
                    <strong>{{ $b->departure_date ? $b->departure_date->format('d M Y') : '-' }}</strong>
                </td>
                <td style="text-align:center;">
                    <span class="badge badge-info" style="font-weight:700; font-size:0.82rem;">
                        {{ $b->participants }} Pax
                    </span>
                </td>
                <td style="font-weight:600; color:var(--teal);">
                    {{ $b->total_price_formatted }}
                </td>
                <td>
                    @php $sc = match($b->booking_status) { 'confirmed','completed'=>'success', 'cancelled'=>'danger', 'waiting_verification','processing'=>'warning', default=>'info' }; @endphp
                    <span class="badge badge-{{ $sc }}">{{ $b->booking_status_label }}</span>
                </td>
                <td>
                    @php $pc = match($b->payment_status) { 'paid'=>'success', 'failed','refund'=>'danger', 'dp_paid'=>'warning', default=>'muted' }; @endphp
                    <span class="badge badge-{{ $pc }}">{{ $b->payment_status_label }}</span>
                    @if($b->payment_status === 'dp_paid')
                        <div style="font-size:0.7rem; color:#f59e0b; margin-top:2px;">
                            Sisa: Rp {{ number_format($b->remaining_amount, 0, ',', '.') }}
                        </div>
                    @endif
                </td>
                <td style="text-align:right;">
                    <a href="{{ route('admin.bookings.show', $b) }}" class="admin-btn admin-btn--secondary admin-btn--sm">Detail</a>
                    @if(!$b->invoice_number)
                        <a href="{{ route('admin.bookings.manualInvoiceForm', $b) }}" class="admin-btn admin-btn--primary admin-btn--sm" style="margin-left:4px;">Invoice</a>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="text-align:center; color:var(--text-muted); padding:40px;">
                    Belum ada data pemesanan yang sesuai dengan filter yang dipilih.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <div class="pagination" style="padding:16px 20px;">
        {{ $bookings->appends(request()->all())->links() }}
    </div>
</div>
@endsection
