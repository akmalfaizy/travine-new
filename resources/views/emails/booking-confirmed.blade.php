<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Dikonfirmasi</title>
</head>
<body style="margin:0; padding:0; background:#f0f4f8; font-family: Arial, sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8; padding:30px 0;">
    <tr><td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.08);">
            {{-- Header --}}
            <tr>
                <td style="background:linear-gradient(135deg,#0f2a52,#14B8A6); padding:32px; text-align:center;">
                    <h1 style="color:#fff; font-size:24px; margin:0;">travine<span style="color:#a7f3d0;">.id</span></h1>
                    <p style="color:rgba(255,255,255,0.8); margin:6px 0 0; font-size:13px;">Jelajahi Keindahan Nusantara</p>
                </td>
            </tr>
            {{-- Icon + Title --}}
            <tr>
                <td style="padding:32px; text-align:center;">
                    <div style="font-size:48px; margin-bottom:12px;">✅</div>
                    <h2 style="color:#0f2a52; margin:0; font-size:22px;">Pemesanan Dikonfirmasi!</h2>
                    <p style="color:#718096; margin-top:8px; font-size:14px;">Halo <strong>{{ $booking->name }}</strong>, pemesanan Anda telah berhasil dikonfirmasi.</p>
                </td>
            </tr>
            {{-- Booking Details --}}
            <tr>
                <td style="padding:0 32px 24px;">
                    <table width="100%" cellpadding="12" cellspacing="0" style="background:#f7fafc; border-radius:8px; border:1px solid #e2e8f0;">
                        <tr><td colspan="2" style="border-bottom:1px solid #e2e8f0; font-weight:bold; color:#0f2a52; padding-bottom:10px;">Detail Pemesanan</td></tr>
                        <tr><td style="color:#718096; width:180px;">Kode Booking</td><td style="font-weight:bold; color:#14B8A6;">{{ $booking->booking_code }}</td></tr>
                        <tr style="background:#fff;"><td style="color:#718096;">Paket Wisata</td><td>{{ $booking->tour?->title }}</td></tr>
                        <tr><td style="color:#718096;">Tanggal Keberangkatan</td><td>{{ $booking->departure_date->format('d M Y') }}</td></tr>
                        <tr style="background:#fff;"><td style="color:#718096;">Jumlah Peserta</td><td>{{ $booking->participants }} orang</td></tr>
                        <tr><td style="color:#718096; font-weight:bold;">Total Biaya</td><td style="font-weight:bold; color:#0f2a52; font-size:16px;">{{ $booking->total_price_formatted }}</td></tr>
                    </table>
                </td>
            </tr>
            {{-- CTA --}}
            <tr>
                <td style="padding:0 32px 32px; text-align:center;">
                    <a href="{{ url('/bookings/' . $booking->booking_code) }}"
                       style="display:inline-block; background:linear-gradient(135deg,#14B8A6,#0f2a52); color:#fff; padding:14px 32px; border-radius:8px; text-decoration:none; font-weight:bold; font-size:14px;">
                        Lihat Status Pemesanan
                    </a>
                </td>
            </tr>
            {{-- Footer --}}
            <tr>
                <td style="background:#f7fafc; padding:20px 32px; text-align:center; border-top:1px solid #e2e8f0;">
                    <p style="color:#a0aec0; font-size:12px; margin:0;">Terima kasih telah mempercayai travine.id 🌏<br>
                    Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
                </td>
            </tr>
        </table>
    </td></tr>
</table>
</body>
</html>
