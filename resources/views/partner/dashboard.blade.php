@extends('layouts.app')

@section('content')
<section class="section" style="padding-top:120px;min-height:100vh;">
    <div class="container">
        {{-- Header --}}
        <div class="partner-dash__header">
            <div>
                <h1 class="partner-dash__title">Dashboard Mitra</h1>
                <p class="partner-dash__subtitle">Selamat datang, <strong>{{ $partner->name }}</strong>!</p>
            </div>
            <div class="partner-dash__actions">
                <a href="{{ url('/') }}" class="btn btn--outline" style="font-size:0.85rem;padding:10px 20px;">← Kembali ke Website</a>
                <form method="POST" action="{{ route('partner.logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn--outline" style="font-size:0.85rem;padding:10px 20px;color:#f87171;border-color:rgba(248,113,113,0.3);">Logout</button>
                </form>
            </div>
        </div>

        @include('partner.nav')

        {{-- Referral Link --}}
        <div class="partner-dash__referral">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
                <span class="partner-dash__referral-label">🔗 Link Referral Utama Anda:</span>
                <small style="color:var(--teal, #14B8A6); font-size:0.8rem; font-weight:500;">✓ Setiap booking lewat link Anda otomatis menyertakan kode referral ini</small>
            </div>
            <div class="partner-dash__referral-box" style="margin-top:8px;">
                <code id="referral-link">{{ $partner->referral_link }}</code>
                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('referral-link').textContent);this.textContent='✓ Copied!';setTimeout(()=>this.textContent='Copy Link',2000)" class="btn btn--primary" style="padding:8px 16px;font-size:0.8rem;">Copy Link</button>
            </div>
            <p style="font-size:0.825rem; color:var(--text-muted); margin-top:8px; line-height:1.5;">
                💡 <strong>Tips:</strong> Anda juga bisa menambahkan <code>?ref={{ $partner->referral_code }}</code> di akhir URL paket wisata atau halaman destinasi mana saja (contoh: <code>{{ url('/wisata/detail/slug-wisata') }}?ref={{ $partner->referral_code }}</code>).
            </p>
        </div>

        {{-- Stats Grid --}}
        <div class="partner-dash__stats">
            <div class="partner-dash__stat-card">
                <span class="partner-dash__stat-icon">👁️</span>
                <span class="partner-dash__stat-number">{{ number_format($totalClicks) }}</span>
                <span class="partner-dash__stat-label">Total Klik</span>
            </div>
            <div class="partner-dash__stat-card">
                <span class="partner-dash__stat-icon">📋</span>
                <span class="partner-dash__stat-number">{{ number_format($totalBookings) }}</span>
                <span class="partner-dash__stat-label">Total Booking</span>
            </div>
            <div class="partner-dash__stat-card">
                <span class="partner-dash__stat-icon">💰</span>
                <span class="partner-dash__stat-number">Rp {{ number_format($totalSales, 0, ',', '.') }}</span>
                <span class="partner-dash__stat-label">Total Penjualan</span>
            </div>
            <div class="partner-dash__stat-card">
                <span class="partner-dash__stat-icon">🏆</span>
                <span class="partner-dash__stat-number">Rp {{ number_format($totalCommission, 0, ',', '.') }}</span>
                <span class="partner-dash__stat-label">Total Komisi</span>
            </div>
        </div>

        {{-- Commission Breakdown --}}
        <div class="partner-dash__commission-grid">
            <div class="partner-dash__commission-card partner-dash__commission-card--available" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                <div>
                    <span class="partner-dash__commission-label">Komisi Tersedia</span>
                    <span class="partner-dash__commission-amount">Rp {{ number_format($availableCommission, 0, ',', '.') }}</span>
                </div>
                <a href="{{ route('partner.withdrawals') }}" class="btn btn--primary" style="padding:10px 18px; font-size:0.85rem; font-weight:700; border-radius:8px;">
                    💸 Tarik Komisi
                </a>
            </div>
            <div class="partner-dash__commission-card partner-dash__commission-card--processing">
                <span class="partner-dash__commission-label">Sedang Diproses</span>
                <span class="partner-dash__commission-amount">Rp {{ number_format($processingCommission, 0, ',', '.') }}</span>
            </div>
            <div class="partner-dash__commission-card partner-dash__commission-card--paid">
                <span class="partner-dash__commission-label">Sudah Dibayar</span>
                <span class="partner-dash__commission-amount">Rp {{ number_format($paidCommission, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Recent Bookings --}}
        <div class="partner-dash__section">
            <h2 class="partner-dash__section-title">Booking Terbaru</h2>
            @if($recentBookings->count() > 0)
                <div class="partner-dash__table-wrap">
                    <table class="partner-dash__table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Tour</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings as $booking)
                            <tr>
                                <td><code>{{ $booking->booking_code }}</code></td>
                                <td>{{ $booking->tour->name ?? '-' }}</td>
                                <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                <td><span class="partner-dash__badge partner-dash__badge--{{ $booking->booking_status }}">{{ ucfirst($booking->booking_status) }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="partner-dash__empty">Belum ada booking.</p>
            @endif
        </div>

        {{-- Recent Commissions --}}
        <div class="partner-dash__section">
            <h2 class="partner-dash__section-title">Komisi Terbaru</h2>
            @if($recentCommissions->count() > 0)
                <div class="partner-dash__table-wrap">
                    <table class="partner-dash__table">
                        <thead>
                            <tr>
                                <th>Tour</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentCommissions as $commission)
                            <tr>
                                <td>{{ $commission->tour->name ?? '-' }}</td>
                                <td>Rp {{ number_format($commission->amount, 0, ',', '.') }}</td>
                                <td><span class="partner-dash__badge partner-dash__badge--{{ $commission->status }}">{{ ucfirst($commission->status) }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="partner-dash__empty">Belum ada komisi.</p>
            @endif
        </div>

        {{-- Recent Withdrawals --}}
        <div class="partner-dash__section">
            <h2 class="partner-dash__section-title">Penarikan Terbaru</h2>
            @if($recentWithdrawals->count() > 0)
                <div class="partner-dash__table-wrap">
                    <table class="partner-dash__table">
                        <thead>
                            <tr>
                                <th>Jumlah</th>
                                <th>Bank</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentWithdrawals as $withdrawal)
                            <tr>
                                <td>Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</td>
                                <td>{{ $withdrawal->bank_name }} - {{ $withdrawal->account_number }}</td>
                                <td><span class="partner-dash__badge partner-dash__badge--{{ $withdrawal->status }}">{{ ucfirst($withdrawal->status) }}</span></td>
                                <td>{{ $withdrawal->created_at->format('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="partner-dash__empty">Belum ada penarikan.</p>
            @endif
        </div>

    </div>
</section>
@endsection
