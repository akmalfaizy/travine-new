@extends('admin.layout')
@section('title', 'Detail Partner - ' . $partner->name)
@section('content')
<div class="admin-topbar">
    <div>
        <h1>👤 Detail Partner: {{ $partner->name }}</h1>
        <small style="color:var(--text-muted);">Kode Referral: <strong style="color:var(--teal);">{{ $partner->referral_code }}</strong></small>
    </div>
    <a href="{{ route('admin.partners.index') }}" class="admin-btn admin-btn--secondary">← Kembali</a>
</div>

@if(session('success'))
    <div class="admin-alert admin-alert--success">✓ {{ session('success') }}</div>
@endif

{{-- Stats Grid --}}
<div class="admin-grid admin-grid--4" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(20,184,166,0.15); color:var(--teal);">👁️</div>
        <div>
            <div class="stat-value">{{ number_format($totalClicks) }}</div>
            <div class="stat-label">Total Klik Referral</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,0.15); color:#60a5fa;">📋</div>
        <div>
            <div class="stat-value">{{ number_format($totalBookings) }}</div>
            <div class="stat-label">Total Booking Masuk</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(249,115,22,0.15); color:#f97316;">💰</div>
        <div>
            <div class="stat-value">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
            <div class="stat-label">Total Penjualan Paket</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(34,197,94,0.15); color:#22c55e;">🏆</div>
        <div>
            <div class="stat-value">Rp {{ number_format($availableCommission, 0, ',', '.') }}</div>
            <div class="stat-label">Komisi Tersedia (Siap Cair)</div>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:340px 1fr; gap:24px; align-items:start;">
    {{-- Left: Status & Bank Info --}}
    <div style="display:flex; flex-direction:column; gap:20px;">
        {{-- Status Card --}}
        <div class="admin-card">
            <h3 style="font-weight:700; margin-bottom:16px; font-size:1.05rem;">Status Keanggotaan Partner</h3>
            <form method="POST" action="{{ route('admin.partners.status', $partner) }}">
                @csrf
                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label">Ubah Status Partner</label>
                    <select name="status" class="form-control" style="width:100%;">
                        <option value="active" {{ $partner->status === 'active' ? 'selected' : '' }}>🟢 Active (Aktif)</option>
                        <option value="suspended" {{ $partner->status === 'suspended' ? 'selected' : '' }}>🔴 Suspended (Ditangguhkan)</option>
                        <option value="inactive" {{ $partner->status === 'inactive' ? 'selected' : '' }}>⚪ Inactive (Nonaktif)</option>
                    </select>
                </div>
                <button type="submit" class="admin-btn admin-btn--primary" style="width:100%; justify-content:center;">
                    Update Status Partner
                </button>
            </form>
        </div>

        {{-- Bank Details Card --}}
        <div class="admin-card">
            <h3 style="font-weight:700; margin-bottom:16px; font-size:1.05rem;">🏦 Rekening Pembayaran</h3>
            @if($partner->bank_name)
                <div style="font-size:0.9rem; line-height:1.6;">
                    <div><strong>Bank:</strong> {{ $partner->bank_name }}</div>
                    <div><strong>No. Rekening:</strong> <span style="color:var(--teal); font-weight:700;">{{ $partner->bank_account_number }}</span></div>
                    <div><strong>Atas Nama:</strong> {{ $partner->bank_account_name }}</div>
                </div>
            @else
                <span style="color:#ef4444; font-size:0.85rem;">Partner belum melengkapi informasi rekening bank.</span>
            @endif
        </div>

        {{-- Personal Info Card --}}
        <div class="admin-card">
            <h3 style="font-weight:700; margin-bottom:16px; font-size:1.05rem;">📍 Informasi Pribadi</h3>
            <div style="font-size:0.85rem; line-height:1.6; color:var(--text-secondary);">
                <div><strong>Email:</strong> {{ $partner->email }}</div>
                <div><strong>Telepon/WA:</strong> {{ $partner->phone }}</div>
                <div><strong>Alamat:</strong> {{ $partner->address ?: '-' }}</div>
                <div><strong>Kota/Provinsi:</strong> {{ $partner->city ?: '-' }}, {{ $partner->province ?: '-' }}</div>
                <div><strong>Terdaftar pada:</strong> {{ $partner->created_at->format('d M Y, H:i') }}</div>
            </div>
        </div>
    </div>

    {{-- Right: Activity Tables --}}
    <div style="display:flex; flex-direction:column; gap:20px;">
        {{-- Recent Bookings --}}
        <div class="admin-card">
            <h3 style="font-weight:700; margin-bottom:16px; font-size:1.05rem;">📋 Booking dari Link Referral</h3>
            @if($partner->bookings->count() > 0)
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Kode Booking</th>
                            <th>Pemesan</th>
                            <th>Paket Wisata</th>
                            <th>Total Harga</th>
                            <th>Status Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($partner->bookings->take(5) as $b)
                            <tr>
                                <td><a href="{{ route('admin.bookings.show', $b) }}" style="color:var(--teal); font-weight:700;">{{ $b->booking_code }}</a></td>
                                <td>{{ $b->name }}<br><small style="color:var(--text-muted);">{{ $b->whatsapp }}</small></td>
                                <td>{{ $b->tour?->title }}</td>
                                <td style="font-weight:700;">Rp {{ number_format($b->total_price, 0, ',', '.') }}</td>
                                <td>
                                    @if(in_array($b->booking_status, ['confirmed', 'completed']))
                                        <span class="badge badge-success">Disetujui Admin</span>
                                    @elseif($b->booking_status === 'waiting_verification')
                                        <span class="badge badge-warning">Menunggu Konfirmasi</span>
                                    @else
                                        <span class="badge badge-muted">{{ $b->booking_status_label }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="color:var(--text-muted); padding:20px; text-align:center;">Belum ada booking masuk via referral partner ini.</div>
            @endif
        </div>

        {{-- Recent Withdrawals --}}
        <div class="admin-card">
            <h3 style="font-weight:700; margin-bottom:16px; font-size:1.05rem;">💸 Riwayat Penarikan Komisi</h3>
            @if($partner->withdrawals->count() > 0)
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Kode Request</th>
                            <th>Nominal</th>
                            <th>Tanggal</th>
                            <th>Status Transfer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($partner->withdrawals->take(5) as $w)
                            <tr>
                                <td><a href="{{ route('admin.withdrawals.show', $w) }}" style="color:var(--teal); font-weight:700;">{{ $w->withdrawal_code }}</a></td>
                                <td style="font-weight:700;">Rp {{ number_format($w->amount, 0, ',', '.') }}</td>
                                <td style="color:var(--text-muted); font-size:0.85rem;">{{ $w->created_at->format('d M Y, H:i') }}</td>
                                <td>
                                    @if($w->status === 'paid')
                                        <span class="badge badge-success">✓ Dibayar</span>
                                    @elseif($w->status === 'requested')
                                        <span class="badge badge-warning">Menunggu Transfer</span>
                                    @else
                                        <span class="badge badge-danger">{{ $w->status_label }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="color:var(--text-muted); padding:20px; text-align:center;">Belum ada pengajuan penarikan komisi.</div>
            @endif
        </div>
    </div>
</div>
@endsection
