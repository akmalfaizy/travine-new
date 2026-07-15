<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Konfirmasi Pemesanan – travine.id</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; color: #1a202c; background: #f7fafc; margin: 0; padding: 0; }
        .wrap { max-width: 620px; margin: 32px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #0F2A52 0%, #14B8A6 100%); padding: 32px; text-align: center; }
        .header h1 { margin: 0; color: #fff; font-size: 22px; font-weight: 700; letter-spacing: 1px; }
        .header p { margin: 6px 0 0; color: rgba(255,255,255,0.8); font-size: 13px; }
        .body { padding: 36px 32px; }
        .greeting { font-size: 16px; line-height: 1.6; margin-bottom: 24px; }
        .status-badge { display: inline-block; background: #FEF3C7; color: #92400E; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; margin-bottom: 24px; }
        .status-badge.verified { background: #D1FAE5; color: #065F46; }
        .section-title { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin: 28px 0 12px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: top; }
        .info-table td:first-child { font-weight: 600; color: #64748b; width: 45%; }
        .info-table td:last-child { color: #1e293b; font-weight: 500; }
        .price-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; margin: 24px 0; }
        .price-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; font-size: 14px; border-bottom: 1px solid #e2e8f0; }
        .price-row:last-child { border: none; padding-top: 12px; font-size: 16px; font-weight: 700; color: #0F2A52; }
        .price-row .label { color: #64748b; }
        .price-row .amount-dp { color: #F97316; font-weight: 700; }
        .alert-box { background: #FEF3C7; border-left: 4px solid #F59E0B; padding: 14px 18px; border-radius: 0 8px 8px 0; margin: 24px 0; font-size: 13px; line-height: 1.6; color: #78350F; }
        .alert-box.success { background: #D1FAE5; border-color: #10B981; color: #064E3B; }
        .cta-btn { display: block; width: fit-content; margin: 28px auto 0; background: linear-gradient(135deg, #14B8A6, #0d9488); color: #fff !important; padding: 13px 30px; text-decoration: none; border-radius: 8px; font-weight: 700; font-size: 15px; text-align: center; }
        .wa-btn { display: block; width: fit-content; margin: 12px auto 0; background: #25D366; color: #fff !important; padding: 11px 26px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; text-align: center; }
        .terms { background: #f8fafc; border-radius: 8px; padding: 16px 18px; margin: 24px 0; font-size: 12px; color: #64748b; line-height: 1.7; }
        .terms ul { margin: 6px 0; padding-left: 18px; }
        .footer { background: #f8fafc; padding: 20px 32px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
        .booking-code { font-size: 22px; font-weight: 800; color: #0F2A52; background: #f1f5f9; border-radius: 8px; padding: 10px 20px; display: inline-block; letter-spacing: 2px; margin: 8px 0 20px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h1>🧳 travine.id</h1>
        <p>Konfirmasi Pemesanan Wisata Anda</p>
    </div>
    <div class="body">
        <p class="greeting">Halo <strong>{{ $booking->name }}</strong>,</p>
        <p style="font-size:14px; color:#475569; line-height:1.7; margin-bottom:20px;">
            Terima kasih telah memesan paket wisata bersama <strong>travine.id</strong>! Berikut adalah ringkasan dan detail pemesanan Anda.
        </p>

        <div style="text-align:center;">
            <span class="booking-code">{{ $booking->booking_code }}</span>
        </div>

        <span class="status-badge">⏳ Menunggu Verifikasi Admin</span>

        <div class="section-title">Detail Pemesanan</div>
        <table class="info-table">
            <tr><td>Paket Wisata</td><td>{{ $booking->tour->title }}</td></tr>
            <tr><td>Lokasi</td><td>{{ $booking->tour->location }}</td></tr>
            <tr><td>Tanggal Keberangkatan</td><td>{{ $booking->departure_date->translatedFormat('d F Y') }}</td></tr>
            @php $isTransport = $booking->tour?->category?->slug === 'transportasi'; @endphp
            @if($isTransport)
            <tr><td>Durasi Sewa</td><td>{{ $booking->duration_days }} Hari</td></tr>
            @else
            <tr><td>Jumlah Peserta</td><td>{{ $booking->participants }} Orang</td></tr>
            @endif
            @if($booking->notes)
            <tr><td>Catatan</td><td>{{ $booking->notes }}</td></tr>
            @endif
        </table>

        <div class="section-title">Rincian Pembayaran</div>
        <div class="price-box">
            <div class="price-row">
                <span class="label">Harga × Kuantitas</span>
                <span>Rp {{ number_format($booking->tour->price, 0, ',', '.') }} × {{ $isTransport ? $booking->duration_days . ' hari' : $booking->participants . ' orang' }}</span>
            </div>
            @if($booking->discount > 0)
            <div class="price-row">
                <span class="label" style="color:#ef4444;">Diskon</span>
                <span style="color:#ef4444;">-{{ $booking->discount_formatted }}</span>
            </div>
            @endif
            <div class="price-row">
                <span class="label">Total Biaya</span>
                <span>{{ $booking->total_price_formatted }}</span>
            </div>
            <div class="price-row" style="background:#fff8f0; padding:12px; border-radius:8px; margin-top:4px; border:1px dashed #F97316;">
                <span class="label">Metode Bayar</span>
                <span class="amount-dp">
                    @if($booking->payment_type === 'dp')
                        DP ({{ $booking->participants }} × Rp 100.000) = {{ $booking->paid_amount_formatted }}
                    @else
                        Lunas – {{ $booking->paid_amount_formatted }}
                    @endif
                </span>
            </div>
            @if($booking->payment_type === 'dp')
            <div class="price-row" style="font-size:13px;">
                <span class="label">Sisa Pelunasan</span>
                <span style="color:#64748b;">Rp {{ number_format($booking->remaining_amount, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>

        <div class="alert-box">
            ⚠️ <strong>Perhatian:</strong> Bukti pembayaran Anda sedang dalam proses verifikasi oleh tim kami. Proses ini membutuhkan waktu maksimal <strong>1×24 jam</strong>. Jika lebih dari waktu tersebut belum ada konfirmasi, silakan hubungi admin.
            @if($booking->payment_type === 'dp')
            <br><br>
            📅 Ingat: Sisa pelunasan wajib dibayar paling lambat <strong>H-1 sebelum keberangkatan</strong>. Jika tidak dilunasi, slot pemesanan dapat hangus.
            @endif
        </div>

        <div style="text-align:center; margin-top:28px;">
            <a href="{{ url('/bookings/' . $booking->booking_code) }}" class="cta-btn">🔍 Pantau Status Pemesanan</a>
            <a href="https://wa.me/{{ \App\Models\Setting::get('phone_whatsapp', '6282137925585') }}?text=Halo%20Admin%20Travine,%20saya%20sudah%20upload%20bukti%20bayar%20untuk%20booking%20{{ urlencode($booking->booking_code) }}" class="wa-btn" target="_blank">💬 Hubungi Admin via WhatsApp</a>
        </div>

        <div class="terms">
            <strong>📋 Ringkasan Syarat & Ketentuan:</strong>
            <ul>
                <li>DP Rp 100.000/pax sebagai tanda jadi. Sistem <em>first come, first served</em>.</li>
                <li>Pelunasan wajib sebelum H-1 keberangkatan.</li>
                <li>Refund 100% jika pembatalan dalam 1×24 jam setelah bayar.</li>
                <li>Setelah batas waktu: tidak dapat direfund, namun bisa alih jadwal 1x.</li>
                <li>Jika trip dibatalkan penyelenggara: refund 100% atau alih jadwal gratis.</li>
            </ul>
        </div>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} travine.id – Jelajahi Keindahan Nusantara<br>
        Email ini dikirim otomatis, mohon tidak membalas email ini.
    </div>
</div>
</body>
</html>
