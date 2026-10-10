@extends('layouts.app')

@section('content')
<section class="section" style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding-top:120px;">
    <div class="container" style="max-width:480px;">
        <div class="partner-auth-card">
            <div class="partner-auth-card__header">
                <span class="section__badge">🛡️ Verifikasi 2FA</span>
                <h1 class="partner-auth-card__title">Verifikasi Kode OTP</h1>
                <p class="partner-auth-card__subtitle">
                    Kami telah mengirimkan 6 digit kode verifikasi ke email
                    <strong style="color:var(--teal);">{{ isset($partner) ? $partner->email : 'Anda' }}</strong>.
                </p>
            </div>

            @if(session('success'))
                <div class="partner-auth-alert partner-auth-alert--success">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="partner-auth-alert partner-auth-alert--error">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    <div>
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('partner.otp') }}" class="partner-auth-form">
                @csrf
                <div class="partner-auth-form__group" style="text-align:center;">
                    <label class="partner-auth-form__label" for="otp" style="display:block; text-align:center; font-weight:700; margin-bottom:10px;">
                        Masukkan 6 Digit Kode OTP
                    </label>
                    <input class="partner-auth-form__input"
                           type="text"
                           name="otp"
                           id="otp"
                           required
                           maxlength="6"
                           pattern="[0-9]{6}"
                           inputmode="numeric"
                           autocomplete="one-time-code"
                           placeholder="••••••"
                           style="font-size:2rem; letter-spacing:14px; text-align:center; font-weight:800; padding:12px; font-family:monospace; background:rgba(20,184,166,0.06); border-color:var(--teal);">
                    <small style="display:block; color:var(--text-muted); font-size:0.8rem; margin-top:8px;">
                        Kode berlaku selama 10 menit. Periksa juga folder Spam/Junk email Anda.
                    </small>
                </div>

                <button type="submit" class="btn btn--primary partner-auth-form__submit" style="width:100%; justify-content:center; margin-top:16px;">
                    <span>Verifikasi &amp; Masuk</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
            </form>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px; font-size:0.85rem; padding-top:16px; border-top:1px solid var(--glass-border);">
                <form method="POST" action="{{ route('partner.otp.resend') }}" style="display:inline;">
                    @csrf
                    <button type="submit" style="background:none; border:none; color:var(--teal); font-weight:600; cursor:pointer; text-decoration:underline;">
                        ↺ Kirim Ulang OTP
                    </button>
                </form>

                <a href="{{ route('partner.login') }}" style="color:var(--text-muted); text-decoration:none;">
                    ← Kembali ke Login
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
