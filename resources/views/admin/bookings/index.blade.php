@extends('admin.layout')
@section('title', 'Manajemen Pemesanan')
@section('content')
<div class="admin-topbar">
    <h1>Manajemen Pemesanan</h1>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('admin.bookings.create') }}" class="admin-btn admin-btn--primary">
            + Buat Booking
        </a>
        <a href="{{ route('admin.bookings.export') }}" class="admin-btn admin-btn--secondary">
            ↓ Export CSV
        </a>
        <a href="{{ route('admin.bookings.export-insurance') }}"
           class="admin-btn admin-btn--secondary"
           style="border-color:rgba(251,191,36,0.3);color:#fbbf24;"
           title="Export data peserta untuk keperluan asuransi (satu baris per peserta)">
            🛡 Export CSV Asuransi
        </a>
    </div>
</div>

{{-- Filter --}}
<div class="admin-card" style="padding:16px 20px; margin-bottom:16px;">
    <form method="GET" style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
        <div>
            <label class="form-label" style="font-size:0.78rem;">Status Booking</label>
            <select name="status" class="form-control" style="width:180px;">
                <option value="">Semua Status</option>
                @foreach(['waiting_payment'=>'Menunggu Bayar','waiting_verification'=>'Verifikasi','processing'=>'Diproses','confirmed'=>'Dikonfirmasi','departed'=>'Berangkat','completed'=>'Selesai','cancelled'=>'Dibatalkan'] as $v=>$l)
                    <option value="{{ $v }}" {{ request('status') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label" style="font-size:0.78rem;">Status Bayar</label>
            <select name="payment" class="form-control" style="width:160px;">
                <option value="">Semua</option>
                @foreach(['unpaid'=>'Belum Bayar','dp_paid'=>'DP Dibayar','paid'=>'Lunas','refund'=>'Refund','failed'=>'Gagal'] as $v=>$l)
                    <option value="{{ $v }}" {{ request('payment') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label" style="font-size:0.78rem;">Cari</label>
            <input name="search" type="text" class="form-control" value="{{ request('search') }}" placeholder="Kode/nama/email..." style="width:200px;">
        </div>
        <button type="submit" class="admin-btn admin-btn--primary">Filter</button>
        <a href="{{ route('admin.bookings.index') }}" class="admin-btn admin-btn--secondary">Reset</a>
    </form>
</div>

<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr><th>Kode</th><th>Nama</th><th>Paket Wisata</th><th>Tgl Berangkat</th><th>Total</th><th>Status Booking</th><th>Status Bayar</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        @forelse($bookings as $b)
            <tr>
                <td><a href="{{ route('admin.bookings.show', $b) }}" style="color:var(--teal); font-weight:700; text-decoration:none;">{{ $b->booking_code }}</a></td>
                <td>
                    <div style="font-weight:600;">{{ $b->name }}</div>
                    <div style="font-size:0.78rem; color:var(--text-muted);">{{ $b->whatsapp }}</div>
                </td>
                <td style="font-size:0.85rem; color:var(--text-secondary);">{{ Str::limit($b->tour?->title, 30) }}</td>
                <td style="font-size:0.85rem;">{{ $b->departure_date->format('d M Y') }}</td>
                <td style="font-weight:600; color:var(--teal);">{{ $b->total_price_formatted }}</td>
                <td>
                    @php $sc = match($b->booking_status) { 'confirmed','completed'=>'success', 'cancelled'=>'danger', 'waiting_verification','processing'=>'warning', default=>'info' }; @endphp
                    <span class="badge badge-{{ $sc }}">{{ $b->booking_status_label }}</span>
                </td>
                <td>
                    @php $pc = match($b->payment_status) { 'paid'=>'success', 'failed','refund'=>'danger', 'dp_paid'=>'warning', default=>'muted' }; @endphp
                    <span class="badge badge-{{ $pc }}">{{ $b->payment_status_label }}</span>
                </td>
                <td>
                    <a href="{{ route('admin.bookings.show', $b) }}" class="admin-btn admin-btn--secondary admin-btn--sm">Detail</a>
                    @if(!$b->invoice_number)
                        <a href="{{ route('admin.bookings.manualInvoiceForm', $b) }}" class="admin-btn admin-btn--primary admin-btn--sm" style="margin-left:8px;">Buat Invoice</a>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="8" style="text-align:center; color:var(--text-muted); padding:40px;">Belum ada pemesanan</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="pagination">{{ $bookings->appends(request()->all())->links() }}</div>
</div>
@endsection
