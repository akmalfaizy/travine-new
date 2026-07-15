<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reminder Pemesanan</title>
</head>
<body style="font-family:Arial,Helvetica,sans-serif; background:#f9f9f9; padding:20px;">
    <div style="max-width:600px; margin:auto; background:#fff; padding:30px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
        <h2 style="color:#2c3e50;">Reminder: Pemesanan Anda Besok</h2>
        <p>Hai {{ $booking->name }},</p>
        <p>Kami ingin mengingatkan bahwa pemesanan <strong>{{ $booking->booking_code }}</strong> untuk paket <strong>{{ $booking->tour->title ?? 'Paket Wisata' }}</strong> akan berangkat besok, {{ $booking->departure_date->format('d M Y') }}.</p>
        <p>Total yang harus dibayar: <strong>{{ $booking->total_price_formatted }}</strong></p>
        <p>Silakan cek detail pemesanan di dashboard Anda atau hubungi kami jika ada pertanyaan.</p>
        <p>Terima kasih telah mempercayakan perjalanan Anda kepada kami.</p>
        <hr style="margin:20px 0; border:none; border-top:1px solid #eee;"/>
        <p style="font-size:0.85rem; color:#777;">Ini adalah email otomatis, mohon tidak membalas.</p>
    </div>
</body>
</html>
