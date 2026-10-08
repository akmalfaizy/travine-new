@extends('layouts.app')

@section('content')
@php
    $isTransport = $booking->tour?->category?->slug === 'transportasi';
    $subtotal = $booking->total_price + $booking->discount;
    // $bankName, $bankAccount, $bankHolder, $whatsAppNumber, $dpAmount injected by controller
@endphp

<style>
    .booking-page {
        padding: 120px 0 80px;
        background: var(--bg-main);
        color: var(--text-primary);
        min-height: 100vh;
    }
    .booking-grid {
        display: grid;
        grid-template-columns: 3fr 2fr;
        gap: 30px;
        margin-top: 30px;
    }
    @media (max-width: 991px) {
        .booking-grid {
            grid-template-columns: 1fr;
        }
    }
    .booking-card {
        background: var(--card-bg, #fff);
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        border: 1px solid rgba(0,0,0,0.03);
    }
    .status-banner {
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
    }
    /* Status classes */
    .status--waiting_payment {
        background: #FEF3C7;
        color: #92400E;
        border-left: 5px solid #F59E0B;
    }
    .status--waiting_verification {
        background: #DBEAFE;
        color: #1E40AF;
        border-left: 5px solid #3B82F6;
    }
    .status--confirmed, .status--completed {
        background: #D1FAE5;
        color: #065F46;
        border-left: 5px solid #10B981;
    }
    .status--cancelled {
        background: #FEE2E2;
        color: #991B1B;
        border-left: 5px solid #EF4444;
    }
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge--unpaid { background: #F59E0B; color: #fff; }
    .badge--dp_paid { background: #3B82F6; color: #fff; }
    .badge--paid { background: #10B981; color: #fff; }
    .badge--failed { background: #EF4444; color: #fff; }

    .detail-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    .detail-table td {
        padding: 12px 0;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    .detail-table td:first-child {
        font-weight: 600;
        color: var(--text-muted);
        width: 40%;
    }

    /* Bank Transfer Card */
    .bank-transfer-card {
        background: var(--bg-offset, #f8fafc);
        border: 1.5px solid rgba(0,0,0,0.07);
        border-radius: 14px;
        padding: 16px 18px;
        margin: 16px 0;
    }
    .bank-logo-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    .bank-logo-row img { height: 24px; width: auto; }
    .bank-logo-row .bank-label {
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.5px;
    }
    .bank-account-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin: 6px 0 6px;
    }
    .account-number {
        font-size: 1.25rem;
        font-weight: 800;
        font-family: 'JetBrains Mono', 'Fira Code', 'Courier New', monospace;
        letter-spacing: 2px;
        color: var(--text-primary);
    }
    .copy-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(20,184,166,0.1);
        border: 1px solid rgba(20,184,166,0.25);
        color: var(--teal, #14B8A6);
        padding: 5px 11px;
        border-radius: 6px;
        font-size: 0.73rem;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s;
        white-space: nowrap;
    }
    .copy-btn:hover { background: var(--teal, #14B8A6); color: #fff; }
    .copy-btn.copied { background: #10B981; color: #fff; border-color: #10B981; }

    /* QRIS Card */
    .qris-card-show {
        text-align: center;
        background: rgba(20,184,166,0.03);
        border: 1px solid rgba(20,184,166,0.2);
        border-radius: 12px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 16px;
    }
    .qris-img-wrap {
        position: relative;
        display: inline-block;
        max-width: 160px;
        cursor: pointer;
        border-radius: 10px;
        overflow: hidden;
        border: 2px solid rgba(20,184,166,0.25);
        background: #fff;
        padding: 5px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.07);
        transition: transform .25s, box-shadow .25s;
    }
    .qris-img-wrap:hover { transform: scale(1.02); box-shadow: 0 8px 24px rgba(20,184,166,0.2); }
    .qris-img-wrap img { width: 100%; border-radius: 7px; display: block; }
    .qris-zoom-badge {
        position: absolute;
        bottom: 8px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(15,23,42,0.85);
        color: #fff;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        gap: 3px;
        white-space: nowrap;
        pointer-events: none;
    }
    .qris-img-wrap:hover .qris-zoom-badge { background: var(--teal, #14B8A6); }
    .qris-show-buttons {
        display: flex;
        gap: 8px;
        margin-top: 10px;
    }
    .btn-qshow {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 13px;
        border-radius: 7px;
        font-size: 0.76rem;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: all .2s;
        border: none;
    }
    .btn-qshow-zoom {
        background: #fff;
        border: 1px solid #cbd5e1;
        color: #334155;
    }
    .btn-qshow-zoom:hover { background: var(--teal, #14B8A6); color: #fff; border-color: var(--teal, #14B8A6); }
    .btn-qshow-dl {
        background: var(--teal, #14B8A6);
        color: #fff;
        border: 1px solid var(--teal, #14B8A6);
    }
    .btn-qshow-dl:hover { background: #0d9488; box-shadow: 0 4px 12px rgba(20,184,166,0.3); }

    .participant-detail-card {
        background: var(--bg-offset, #f8fafc);
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 12px;
        padding: 16px 18px;
        margin-bottom: 12px;
    }
    .participant-detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px 20px;
        margin-top: 10px;
    }
    .participant-detail-item label {
        font-size: 0.74rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 2px;
    }
    .participant-detail-item span {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-primary);
    }
    /* QRIS Lightbox Modal */
    .qris-modal {
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(8px);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: qrisFadeIn .2s ease-out;
    }
    .qris-modal.active { display: flex; }
    .qris-modal-content {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        max-width: 440px;
        width: 100%;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
        position: relative;
        text-align: center;
        animation: qrisPopIn .25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .qris-modal-close {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #f1f5f9;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #475569;
        font-size: 20px;
        line-height: 1;
        transition: all .2s;
    }
    .qris-modal-close:hover { background: #e2e8f0; color: #0f172a; }
    .qris-modal-img {
        width: 100%;
        max-width: 340px;
        height: auto;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        display: block;
        margin: 0 auto 16px;
    }
    .qris-modal-header { margin-bottom: 14px; text-align: center; }
    .qris-modal-header h3 { font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
    .qris-modal-header p { font-size: 0.78rem; color: #64748b; margin: 0; }
    .qris-modal-actions { display: flex; gap: 10px; justify-content: center; margin-top: 16px; }
    @keyframes qrisFadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes qrisPopIn { from { opacity: 0; transform: scale(0.92); } to { opacity: 1; transform: scale(1); } }
</style>

<div class="booking-page">
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; margin-bottom:20px;">
            <div>
                <span style="font-weight:600; color:var(--text-muted);">{{ __('Pemesanan Wisata') }}</span>
                <h1 style="font-size: 2.2rem; font-weight: 850;">{{ __('Kode Booking') }}: <span style="font-family:monospace; letter-spacing:3px;">{{ $booking->booking_code }}</span></h1>
            </div>
            @if($booking->invoice_number && in_array($booking->booking_status, ['confirmed', 'departed', 'completed']))
                <a href="{{ route('bookings.invoice', $booking->booking_code) }}" class="btn btn--primary" style="text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                    </svg>
                    {{ __('Download Invoice (PDF)') }}
                </a>
            @endif
        </div>

        @if(session('success'))
            <div style="background: #D1FAE5; color: #065F46; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-weight:500;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Status Banner --}}
        <div class="status-banner status--{{ $booking->booking_status }}">
            <div>
                <h3 style="margin:0 0 6px; font-size:1.15rem; font-weight:700;">{{ __('Status Pemesanan') }}: {{ $booking->booking_status_label }}</h3>
                <p style="margin:0; font-size:0.95rem;">
                    @if($booking->booking_status == 'waiting_payment')
                        {{ __('Harap selesaikan pembayaran Down Payment (DP) atau Lunas untuk mengonfirmasi pesanan Anda.') }}
                    @elseif($booking->booking_status == 'waiting_verification')
                        {{ __('Pembayaran Anda sedang dalam proses verifikasi oleh tim keuangan kami.') }}
                    @elseif($booking->booking_status == 'confirmed')
                        {{ __('Selamat! Pembayaran Anda telah terverifikasi dan pemesanan dikonfirmasi. Sampai jumpa di hari keberangkatan!') }}
                    @elseif($booking->booking_status == 'cancelled')
                        {{ __('Pemesanan Anda telah dibatalkan. Hubungi admin jika ini merupakan kekeliruan.') }}
                    @else
                        {{ __('Status pemesanan saat ini adalah') }}: {{ $booking->booking_status_label }}.
                    @endif
                </p>
            </div>
            <div style="display:flex; flex-direction:column; align-items:flex-end; gap:6px;">
                <span class="status-badge badge--{{ $booking->payment_status }}">{{ __('Status Bayar') }}: {{ $booking->payment_status_label }}</span>
            </div>
        </div>

        <div class="booking-grid">
            {{-- Left column: Booking Details --}}
            <div class="booking-card">
                <h2 style="font-size:1.4rem; font-weight:750; border-bottom:2px solid rgba(0,0,0,0.06); padding-bottom:12px; margin-bottom:20px;">{{ __('Rincian Pemesanan') }}</h2>
                
                <h3 style="font-size:1.15rem; font-weight:700; color:var(--teal, #14B8A6); margin-bottom:5px;">{{ $booking->tour->title }}</h3>
                <p style="color:var(--text-muted); font-size:0.9rem; margin-top:0;">📍 {{ $booking->tour->location }} | ⏳ {{ $booking->tour->duration }}</p>

                <table class="detail-table">
                    <tr>
                        <td>{{ __('Nama Pemesan') }}</td>
                        <td>{{ $booking->name }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('No. WhatsApp / HP') }}</td>
                        <td>{{ $booking->whatsapp }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('Alamat Email') }}</td>
                        <td>{{ $booking->email }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('Tanggal Keberangkatan') }}</td>
                        <td>{{ $booking->departure_date ? $booking->departure_date->translatedFormat('d F Y') : '-' }}</td>
                    </tr>
                    @if($isTransport)
                        <tr>
                            <td>{{ __('Durasi Sewa') }}</td>
                            <td>{{ $booking->duration_days }} {{ __('Hari') }}</td>
                        </tr>
                    @else
                        <tr>
                            <td>{{ __('Jumlah Peserta') }}</td>
                            <td>{{ $booking->participants }} {{ __('Orang') }}</td>
                        </tr>
                    @endif
                    @if($booking->notes)
                        <tr>
                            <td>{{ __('Catatan Tambahan') }}</td>
                            <td>{{ $booking->notes }}</td>
                        </tr>
                    @endif
                    @if($booking->admin_notes)
                        <tr>
                            <td>{{ __('Catatan Admin') }}</td>
                            <td style="color:#B45309; font-weight:600;">{{ $booking->admin_notes }}</td>
                        </tr>
                    @endif
                </table>

                @if(!empty($booking->participants_data) && is_array($booking->participants_data))
                <h2 style="font-size:1.35rem; font-weight:750; border-bottom:2px solid rgba(0,0,0,0.06); padding-bottom:12px; margin:40px 0 20px; display:flex; align-items:center; gap:10px;">
                    <span>🛡️</span> {{ __('Data Peserta untuk Asuransi Perjalanan') }}
                    <span style="font-size:0.8rem; font-weight:600; background:rgba(20,184,166,0.1); color:var(--teal,#14B8A6); padding:4px 10px; border-radius:999px;">
                        {{ count($booking->participants_data) }} {{ __('Peserta Terdaftar') }}
                    </span>
                </h2>

                <div>
                    @foreach($booking->participants_data as $index => $p)
                    <div class="participant-detail-card">
                        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px dashed rgba(0,0,0,0.08); padding-bottom:8px; margin-bottom:12px;">
                            <strong style="font-size:0.98rem; color:var(--text-primary); display:flex; align-items:center; gap:8px;">
                                <span>👤</span> {{ $p['name'] ?? '-' }}
                                @if($index === 0)
                                    <span style="font-size:0.7rem; background:rgba(20,184,166,0.12); color:var(--teal,#14B8A6); padding:2px 8px; border-radius:99px; font-weight:700;">PIC / Pemesan</span>
                                @endif
                            </strong>
                            <span style="font-size:0.78rem; color:var(--text-muted); font-weight:600;">#{{ $index + 1 }} {{ __('dari') }} {{ count($booking->participants_data) }}</span>
                        </div>
                        <div class="participant-detail-grid">
                            <div class="participant-detail-item">
                                <label>{{ __('NIK / No. KTP / KITAS') }}</label>
                                <span>{{ $p['nik'] ?? '-' }}</span>
                            </div>
                            <div class="participant-detail-item">
                                <label>{{ __('Tanggal Lahir') }}</label>
                                <span>{{ !empty($p['birth_date']) ? \Carbon\Carbon::parse($p['birth_date'])->translatedFormat('d F Y') : '-' }}</span>
                            </div>
                            <div class="participant-detail-item">
                                <label>{{ __('Nomor Telepon / WA') }}</label>
                                <span>{{ $p['phone'] ?? '-' }}</span>
                            </div>
                            <div class="participant-detail-item" style="grid-column: 1 / -1;">
                                <label>{{ __('Alamat Domisili / KTP') }}</label>
                                <span>{{ $p['address'] ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <h2 style="font-size:1.4rem; font-weight:750; border-bottom:2px solid rgba(0,0,0,0.06); padding-bottom:12px; margin:40px 0 20px;">{{ __('Rincian Pembayaran') }}</h2>
                
                <table class="detail-table">
                    <tr>
                        <td>{{ $isTransport ? __('Harga Sewa / Hari') : __('Harga per Orang') }}</td>
                        <td>Rp {{ number_format($booking->tour->price, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>{{ $isTransport ? __('Durasi Sewa') : __('Jumlah Peserta') }}</td>
                        <td>× {{ $isTransport ? ($booking->duration_days . ' ' . __('hari')) : ($booking->participants . ' ' . __('orang')) }}</td>
                    </tr>
                    <tr>
                        <td>Subtotal</td>
                        <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($booking->discount > 0)
                        <tr>
                            <td style="color:#ef4444;">{{ __('Diskon') }}</td>
                            <td style="color:#ef4444;">-{{ $booking->discount_formatted }}</td>
                        </tr>
                    @endif
                    @if($booking->payment_status === 'paid')
                        <tr>
                            <td>{{ __('Jumlah Dibayar (Lunas)') }}</td>
                            <td style="color:var(--teal, #14B8A6); font-weight:600;">{{ $booking->total_price_formatted }}</td>
                        </tr>
                    @else
                        <tr>
                            <td>{{ __('Down Payment (DP) Dibayar') }}</td>
                            <td style="color:var(--orange, #F97316); font-weight:600;">{{ $booking->paid_amount_formatted }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Sisa Pelunasan (Wajib H-1)') }}</td>
                            <td style="color:var(--text-primary); font-weight:700;">Rp {{ number_format($booking->remaining_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </table>
            </div>

            {{-- Right column: Payment upload & Bank info --}}
            <div>
                {{-- Payment instructions --}}
                @if($booking->booking_status == 'waiting_payment')
                    <div class="booking-card" style="margin-bottom: 30px;">
                        <h2 style="font-size:1.3rem; font-weight:750; margin-bottom:16px;">{{ __('Instruksi Pembayaran') }}</h2>
                        <p style="font-size:0.95rem; line-height:1.6; color:var(--text-muted);">
                            {{ __('Silakan lakukan transfer pembayaran Down Payment (DP) minimal sebesar') }} <strong style="color:var(--orange, #F97316);">Rp {{ number_format($dpAmount, 0, ',', '.') }}</strong> ({{ __('atau langsung Lunas sebesar') }} <strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong>) {{ __('melalui transfer Bank atau QRIS resmi:') }}
                        </p>

                        {{-- Bank Transfer --}}
                        <div class="bank-transfer-card">
                            <div class="bank-logo-row">
                                <img src="{{ asset('images/cimb_niaga.svg') }}" alt="CIMB Niaga">
                                <span class="bank-label">{{ __('Transfer Bank') }}</span>
                            </div>
                            <div class="bank-account-row">
                                <span class="account-number" id="show-account-num">{{ $bankAccount }}</span>
                                <button type="button" class="copy-btn" id="show-copy-btn" onclick="copyRekeningShow()">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    <span id="show-copy-text">{{ __('Salin') }}</span>
                                </button>
                            </div>
                            <div style="font-size:0.85rem; color:var(--text-muted);">a.n. <strong style="color:var(--text-primary);">{{ $bankHolder }}</strong></div>
                        </div>

                        {{-- QRIS --}}
                        <div class="qris-card-show">
                            <p style="font-size:0.72rem; font-weight:700; text-transform:uppercase; color:var(--teal,#14B8A6); letter-spacing:.5px; margin:0 0 10px;">{{ __('Atau Scan / Unduh QRIS') }}</p>

                            {{-- Nominal badge --}}
                            <div style="background:linear-gradient(135deg,#0f766e,#14B8A6); color:#fff; border-radius:10px; padding:7px 16px; margin-bottom:10px; text-align:center; width:100%; box-sizing:border-box;">
                                <div style="font-size:0.68rem; font-weight:700; letter-spacing:.5px; text-transform:uppercase; opacity:.85; margin-bottom:2px;">{{ __('Min. DP yang Harus Dibayar') }}</div>
                                <div style="font-size:1.35rem; font-weight:900; letter-spacing:1px; line-height:1.1;">Rp {{ number_format($dpAmount, 0, ',', '.') }}</div>
                                <div style="font-size:0.68rem; opacity:.8; margin-top:2px;">{{ __('atau Lunas') }} Rp {{ number_format($booking->total_price, 0, ',', '.') }}</div>
                            </div>

                            <div class="qris-img-wrap" onclick="openQrisModal()" title="{{ __('Klik untuk perbesar') }}">
                                <img src="{{ asset('images/qris_travine.png') }}" alt="QRIS TRAVINE.ID MANAGEMENT">
                                <div class="qris-zoom-badge">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                                    <span>{{ __('Klik perbesar') }}</span>
                                </div>
                            </div>
                            <div style="margin:8px 0 4px;"><strong style="font-size:0.82rem;">TRAVINE.ID MANAGEMENT</strong></div>
                            <small style="color:var(--text-muted); font-size:0.72rem;">NMID: ID1026610108908 &bull; Standar QRIS Nasional</small>
                            <div class="qris-show-buttons">
                                <button type="button" class="btn-qshow btn-qshow-zoom" onclick="openQrisModal()">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                                    {{ __('Perbesar') }}
                                </button>
                                <a href="{{ asset('images/qris_travine.png') }}" download="QRIS-TRAVINE-ID-MANAGEMENT.png" class="btn-qshow btn-qshow-dl">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    {{ __('Download QRIS') }}
                                </a>
                            </div>
                            <p style="font-size:0.71rem; color:var(--text-muted); margin:8px 0 0; line-height:1.4;">
                                💡 {{ __('Bisa bayar dari CIMB Niaga, BCA, Mandiri, BRI, GoPay, OVO, Dana, ShopeePay, dll.') }}
                            </p>
                        </div>

                        <p style="font-size:0.85rem; color:var(--text-muted); line-height:1.5; border-left:3px solid var(--teal, #14B8A6); padding-left:12px; margin-top:20px;">
                            {{ __('Harap lakukan transfer tepat sesuai nominal di atas. Setelah transfer berhasil, simpan bukti struk/screenshot transfer dan unggah di form di bawah ini.') }}
                        </p>
                    </div>

                    {{-- Form Upload --}}
                    <div class="booking-card">
                        <h2 style="font-size:1.3rem; font-weight:750; margin-bottom:20px;">{{ __('Unggah Bukti Transfer') }}</h2>
                        <form method="POST" action="{{ route('bookings.proof', $booking->booking_code) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label class="form-label" for="payment_proof">{{ __('Pilih File Gambar Bukti Transfer') }} *</label>
                                <input type="file" name="payment_proof" id="payment_proof" class="form-control" accept="image/*" required>
                                <small style="color:var(--text-muted); font-size:0.8rem; display:block; margin-top:6px;">{{ __('Format: JPG, PNG, WEBP. Maksimal ukuran 4MB.') }}</small>
                            </div>
                            <button type="submit" class="btn btn--primary" style="width: 100%; border:none; padding:12px; font-weight:700; border-radius:8px; cursor:pointer;">
                                {{ __('Kirim Bukti Pembayaran') }}
                            </button>
                        </form>
                    </div>
                @elseif($booking->booking_status == 'waiting_verification')
                    <div class="booking-card" style="text-align:center;">
                        <div style="font-size:3rem; margin-bottom:16px;">🔍</div>
                        <h2 style="font-size:1.3rem; font-weight:750; margin-bottom:8px;">{{ __('Bukti Sedang Diverifikasi') }}</h2>
                        <p style="color:var(--text-muted); font-size:0.95rem; line-height:1.6; margin-bottom:24px;">
                            {{ __('Bukti pembayaran Anda telah berhasil dikirim pada tanggal') }} {{ $booking->updated_at->translatedFormat('d M Y, H:i') }} WIB {{ __('dan sedang kami verifikasi. Proses ini membutuhkan waktu maksimal 1x24 jam.') }}
                        </p>
                        @if($booking->payment_proof)
                            <div style="border:1px solid rgba(0,0,0,0.08); border-radius:8px; padding:10px; background:#fafafa;">
                                <span style="font-size:0.8rem; font-weight:600; color:var(--text-muted); display:block; margin-bottom:8px;">{{ __('Bukti yang Anda Kirim') }}:</span>
                                <img src="{{ asset('storage/' . $booking->payment_proof) }}" alt="{{ __('Bukti Transfer') }}" style="max-width:100%; max-height:300px; object-fit:contain; border-radius:4px;">
                            </div>
                        @endif
                        <a href="https://wa.me/{{ $whatsAppNumber }}?text=Halo%20Admin%20travine.id,%20saya%20sudah%20mengunggah%20bukti%20pembayaran%20untuk%20Booking%20Code%3A%20{{ $booking->booking_code }}" class="btn btn--secondary" target="_blank" style="text-decoration:none; display:inline-flex; align-items:center; gap:8px; margin-top:20px; font-weight:600;">
                            💬 {{ __('Hubungi Admin via WhatsApp') }}
                        </a>
                    </div>
                @elseif($booking->booking_status == 'confirmed' || $booking->booking_status == 'completed' || $booking->booking_status == 'departed')
                    <div class="booking-card" style="text-align:center; background: linear-gradient(135deg, #10B981 0%, #059669 100%); color:#fff;">
                        <div style="font-size:3.5rem; margin-bottom:16px;">🎟️</div>
                        <h2 style="font-size:1.4rem; font-weight:800; margin-bottom:8px; color:#fff;">{{ __('Pemesanan Dikonfirmasi!') }}</h2>
                        <p style="font-size:0.95rem; line-height:1.6; margin-bottom:24px; color:rgba(255,255,255,0.9);">
                            {{ __('Terima kasih atas kepercayaan Anda! Pembayaran telah diverifikasi secara penuh. Tiket / Invoice resmi Anda telah diterbitkan. Unduh dan simpan file Invoice PDF tersebut untuk digunakan saat boarding/verifikasi di hari keberangkatan.') }}
                        </p>
                        @if($booking->invoice_number)
                            <a href="{{ route('bookings.invoice', $booking->booking_code) }}" class="btn" style="background:#fff; color:#059669; font-weight:700; text-decoration:none; padding:12px 24px; border-radius:8px; display:inline-flex; align-items:center; gap:8px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                                </svg>
                                {{ __('Unduh Invoice Sekarang') }}
                            </a>
                        @endif
                    </div>
                @else
                    <div class="booking-card" style="text-align:center;">
                        <div style="font-size:3rem; margin-bottom:16px;">⚠️</div>
                        <h2 style="font-size:1.3rem; font-weight:750; margin-bottom:8px;">{{ __('Pembayaran Ditutup') }}</h2>
                        <p style="color:var(--text-muted); font-size:0.95rem; line-height:1.6;">
                            {{ __('Status pemesanan ini telah dibatalkan atau selesai. Transaksi pembayaran tidak dapat dilanjutkan. Silakan hubungi pusat layanan kami jika Anda butuh bantuan.') }}
                        </p>
                        <a href="https://wa.me/{{ $whatsAppNumber }}" class="btn btn--secondary" target="_blank" style="text-decoration:none; margin-top:16px; font-weight:600; display:inline-block;">
                            {{ __('Hubungi Bantuan') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- QRIS Zoom Lightbox Modal --}}
<div id="qrisModal" class="qris-modal" onclick="closeQrisModal()">
    <div class="qris-modal-content" onclick="event.stopPropagation()">
        <button type="button" class="qris-modal-close" onclick="closeQrisModal()" aria-label="Tutup">&times;</button>
        <div class="qris-modal-header">
            <h3>QRIS TRAVINE.ID MANAGEMENT</h3>
            <p>NMID: ID1026610108908 &bull; Standar QRIS Nasional</p>
        </div>
        <img src="{{ asset('images/qris_travine.png') }}" alt="QRIS TRAVINE.ID MANAGEMENT - Full Preview" class="qris-modal-img">
        <div class="qris-modal-actions">
            <a href="{{ asset('images/qris_travine.png') }}" download="QRIS-TRAVINE-ID-MANAGEMENT.png" class="btn" style="background:var(--teal, #14B8A6); color:#fff; font-weight:600; text-decoration:none; padding:10px 20px; font-size:0.85rem; border-radius:8px; display:inline-flex; align-items:center; gap:6px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                <span>{{ __('Download QRIS') }}</span>
            </a>
            <button type="button" onclick="closeQrisModal()" class="btn" style="background:#f1f5f9; color:#334155; font-weight:600; border:none; padding:10px 20px; font-size:0.85rem; border-radius:8px; cursor:pointer;">
                {{ __('Tutup') }}
            </button>
        </div>
        <p style="font-size:0.73rem; color:#64748b; margin-top:12px; text-align:center;">
            {{ __('Scan langsung lewat CIMB Niaga, BCA Mobile, Livin, BRImo, GoPay, OVO, Dana, ShopeePay, atau unduh ke galeri ponsel untuk scan dari file.') }}
        </p>
    </div>
</div>

<script>
    function copyRekeningShow() {
        const num = document.getElementById('show-account-num').textContent.trim();
        navigator.clipboard.writeText(num).then(function() {
            const btn = document.getElementById('show-copy-btn');
            const txt = document.getElementById('show-copy-text');
            btn.classList.add('copied');
            txt.textContent = '{{ __('Tersalin!') }}';
            setTimeout(function() {
                btn.classList.remove('copied');
                txt.textContent = '{{ __('Salin') }}';
            }, 2000);
        });
    }

    function openQrisModal() {
        const modal = document.getElementById('qrisModal');
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeQrisModal() {
        const modal = document.getElementById('qrisModal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('qrisModal');
            if (modal && modal.classList.contains('active')) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }
    });
</script>
@endsection
