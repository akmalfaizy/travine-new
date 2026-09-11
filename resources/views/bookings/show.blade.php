@extends('layouts.app')

@section('content')
@php
    $bankName = \App\Models\Setting::get('bank_name', 'Bank BCA');
    $bankAccount = \App\Models\Setting::get('bank_account', '1234567890');
    $bankHolder = \App\Models\Setting::get('bank_holder', 'PT Travine Indonesia');
    $dpPercentage = intval(\App\Models\Setting::get('dp_percentage', '30'));
    $whatsAppNumber = \App\Models\Setting::get('phone_whatsapp', '6282137925585');
    
    $dpAmount = 100000 * $booking->participants;
    $remainingAmount = $booking->remaining_amount;
    $isTransport = $booking->tour?->category?->slug === 'transportasi';
    $subtotal = $booking->total_price + $booking->discount;
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

    .bank-box {
        background: rgba(15,42,82,0.03);
        border: 1px solid rgba(0,0,0,0.06);
        padding: 20px;
        border-radius: 12px;
        margin: 20px 0;
    }
</style>

<div class="booking-page">
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; margin-bottom:20px;">
            <div>
                <span style="font-weight:600; color:var(--text-muted);">{{ __('Pemesanan Wisata') }}</span>
                <h1 style="font-size: 2.2rem; font-weight: 850;">{{ __('Kode Booking') }}: {{ $booking->booking_code }}</h1>
            </div>
            @if($booking->invoice_number)
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
                            {{ __('Silakan lakukan transfer pembayaran Down Payment (DP) minimal sebesar') }} <strong style="color:var(--orange, #F97316);">Rp {{ number_format($dpAmount, 0, ',', '.') }}</strong> ({{ __('atau langsung Lunas sebesar') }} <strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong>) {{ __('ke rekening resmi berikut:') }}
                        </p>

                        <div class="bank-box">
                            <span style="font-size:0.8rem; text-transform:uppercase; font-weight:700; color:var(--text-muted); display:block; margin-bottom:4px;">{{ $bankName }}</span>
                            <strong style="font-size:1.5rem; color:var(--text-primary); letter-spacing:1px; display:block; margin-bottom:8px;">{{ $bankAccount }}</strong>
                            <span style="font-size:0.9rem; color:var(--text-muted); display:block;">{{ __('Atas Nama') }}: <strong>{{ $bankHolder }}</strong></span>
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
                        <a href="https://wa.me/{{ $whatsAppNumber }}?text=Halo%20Admin%20Travine,%20saya%20sudah%20mengunggah%20bukti%20pembayaran%20untuk%20Booking%20Code%3A%20{{ $booking->booking_code }}" class="btn btn--secondary" target="_blank" style="text-decoration:none; display:inline-flex; align-items:center; gap:8px; margin-top:20px; font-weight:600;">
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
@endsection
