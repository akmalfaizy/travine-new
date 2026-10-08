@extends('layouts.app')

@section('content')
<section class="section" style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding-top:120px;">
    <div class="container" style="max-width:500px;">
        <div class="partner-auth-card">
            <div class="partner-auth-card__header">
                <span class="section__badge">🔐 Partner Login</span>
                <h1 class="partner-auth-card__title">Login Partner</h1>
                <p class="partner-auth-card__subtitle">Masuk ke akun partner Anda untuk mengelola komisi dan referral.</p>
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

            <form method="POST" action="{{ route('partner.login') }}" class="partner-auth-form">
                @csrf
                <div class="partner-auth-form__group">
                    <label class="partner-auth-form__label" for="login">Email</label>
                    <input class="partner-auth-form__input" type="email" name="login" id="login" required value="{{ old('login') }}" placeholder="contoh@email.com">
                </div>
                <div class="partner-auth-form__group">
                    <label class="partner-auth-form__label" for="password">Password</label>
                    <div style="position:relative;">
                        <input class="partner-auth-form__input" type="password" name="password" id="partnerLoginPw" required placeholder="Masukkan password" style="padding-right:48px;">
                        <button type="button" id="partnerLoginEyeBtn" aria-label="Lihat password" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:transparent;border:none;cursor:pointer;padding:6px;color:#9ca3af;display:flex;align-items:center;justify-content:center;border-radius:6px;z-index:5;">
                            <svg id="partnerLoginEyeOpen" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block;pointer-events:none;">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg id="partnerLoginEyeSlash" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;pointer-events:none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="partner-auth-form__remember">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Ingat saya</label>
                </div>
                <button type="submit" class="btn btn--primary partner-auth-form__submit">
                    <span>Login</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
            </form>

            <div class="partner-auth-card__footer">
                <p>Belum punya akun? <a href="{{ route('partner.register') }}" class="partner-auth-card__link">Daftar di sini</a></p>
                <p><a href="{{ route('partner.forgot-password') }}" class="partner-auth-card__link">Lupa password?</a></p>
            </div>
        </div>
    </div>
</section>

<script>
(function() {
    var pwInput  = document.getElementById('partnerLoginPw');
    var eyeOpen  = document.getElementById('partnerLoginEyeOpen');
    var eyeSlash = document.getElementById('partnerLoginEyeSlash');
    var btn      = document.getElementById('partnerLoginEyeBtn');
    if (!btn || !pwInput) return;
    btn.addEventListener('click', function() {
        if (pwInput.type === 'password') {
            pwInput.type = 'text';
            eyeOpen.style.display  = 'none';
            eyeSlash.style.display = 'block';
            btn.setAttribute('aria-label', 'Sembunyikan password');
        } else {
            pwInput.type = 'password';
            eyeOpen.style.display  = 'block';
            eyeSlash.style.display = 'none';
            btn.setAttribute('aria-label', 'Lihat password');
        }
    });
})();
</script>
@endsection
