<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kode OTP Verifikasi Partner</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); padding: 30px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 700; color: #14B8A6; }
        .content { padding: 30px; }
        .otp-box { background: #F1F5F9; border: 2px dashed #0D9488; border-radius: 8px; text-align: center; padding: 20px; margin: 25px 0; }
        .otp-code { font-size: 36px; font-weight: 800; letter-spacing: 8px; color: #0F172A; }
        .footer { background: #F8FAFC; padding: 20px; text-align: center; font-size: 13px; color: #64748B; border-top: 1px solid #E2E8F0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>travine.id Partner</h1>
        </div>
        <div class="content">
            <h2>Halo, {{ $partner->name }}!</h2>
            <p>Terima kasih telah mendaftar sebagai Partner travine.id. Gunakan kode OTP di bawah ini untuk memverifikasi alamat email Anda:</p>

            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
            </div>

            <p>Kode ini berlaku selama <strong>10 menit</strong>. Mohon tidak membagikan kode OTP ini kepada siapapun demi keamanan akun Anda.</p>
            <p>Jika Anda tidak merasa mendaftar di travine.id, silakan abaikan email ini.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} travine.id. All rights reserved.<br>
            Perum Griya Pratama Asri No. 3B, Godean, DIY
        </div>
    </div>
</body>
</html>
