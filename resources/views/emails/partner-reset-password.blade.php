<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Password Partner</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); padding: 30px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 700; color: #14B8A6; }
        .content { padding: 30px; }
        .btn { display: inline-block; background: #0D9488; color: #ffffff !important; padding: 14px 28px; font-weight: 700; text-decoration: none; border-radius: 8px; margin: 20px 0; }
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
            <p>Kami menerima permintaan untuk mereset password akun Partner travine.id Anda.</p>
            <p>Silakan klik tombol di bawah ini untuk membuat password baru:</p>

            <div style="text-align: center;">
                <a href="{{ route('partner.resetPassword', ['token' => $token, 'email' => $partner->email]) }}" class="btn">Reset Password Sekarang</a>
            </div>

            <p>Tautan ini akan kadaluarsa dalam waktu 60 menit. Jika Anda tidak meminta reset password, tidak ada tindakan lebih lanjut yang diperlukan.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} travine.id. All rights reserved.
        </div>
    </div>
</body>
</html>
