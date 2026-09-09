@extends('admin.layout')
@section('title', 'Data Mitra')
@section('content')
<div class="admin-topbar">
    <h1>🤝 Data Mitra (Affiliate)</h1>
</div>

@if(session('success'))
    <div class="admin-alert admin-alert--success">✓ {{ session('success') }}</div>
@endif

{{-- Filters --}}
<div class="admin-card" style="margin-bottom:24px;">
    <form method="GET" action="{{ route('admin.partners.index') }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
        <input type="text" name="search" class="form-control" placeholder="Cari nama, email, phone, kode referral..." value="{{ request('search') }}" style="max-width:320px;">
        <select name="status" class="form-control" style="max-width:180px;">
            <option value="">-- Semua Status --</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        <button type="submit" class="admin-btn admin-btn--primary">Filter</button>
        @if(request('search') || request('status'))
            <a href="{{ route('admin.partners.index') }}" class="admin-btn admin-btn--secondary">Reset</a>
        @endif
    </form>
</div>

<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Mitra</th>
                <th>Kode Referral</th>
                <th>Kontak & Rekening</th>
                <th>Performa</th>
                <th>Komisi Tersedia</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($partners as $partner)
            <tr>
                <td>
                    <div style="font-weight:700; color:var(--text-primary);">{{ $partner->name }}</div>
                    <div style="font-size:0.8rem; color:var(--text-muted);">{{ $partner->email }}</div>
                </td>
                <td>
                    <code style="background:rgba(20,184,166,0.15); color:var(--teal); padding:3px 8px; border-radius:6px; font-weight:700;">{{ $partner->referral_code }}</code>
                </td>
                <td style="font-size:0.85rem;">
                    <div>📱 {{ $partner->phone }}</div>
                    @if($partner->bank_name)
                        <div style="color:var(--text-muted); font-size:0.78rem;">🏦 {{ $partner->bank_name }} ({{ $partner->bank_account_number }})</div>
                    @else
                        <span style="color:#ef4444; font-size:0.75rem;">⚠️ Rekening Belum Diisi</span>
                    @endif
                </td>
                <td style="font-size:0.85rem;">
                    <div>👁️ Clicks: <strong>{{ number_format($partner->clicks_count) }}</strong></div>
                    <div>📋 Bookings: <strong>{{ number_format($partner->bookings_count) }}</strong></div>
                </td>
                <td>
                    <span style="font-weight:700; color:var(--teal);">Rp {{ number_format($partner->available_commission, 0, ',', '.') }}</span>
                    <small style="display:block; font-size:0.75rem; color:var(--text-muted);">Total: Rp {{ number_format($partner->total_commission, 0, ',', '.') }}</small>
                </td>
                <td>
                    @if($partner->status === 'active')
                        <span class="badge badge-success">Aktif</span>
                    @elseif($partner->status === 'pending')
                        <span class="badge badge-warning">Menunggu</span>
                    @else
                        <span class="badge badge-danger">{{ $partner->status_label }}</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.partners.show', $partner) }}" class="admin-btn admin-btn--secondary admin-btn--sm">
                        Detail & Status
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="text-align:center; color:var(--text-muted); padding:40px;">
                    Belum ada data mitra mendaftar.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:20px;">
        {{ $partners->links() }}
    </div>
</div>
@endsection
