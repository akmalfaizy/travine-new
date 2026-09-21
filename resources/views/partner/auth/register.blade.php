@extends('layouts.app')

@section('content')
<section class="section" style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding-top:120px;">
    <div class="container" style="max-width:500px;">
        <div class="partner-auth-card">
            <div class="partner-auth-card__header">
                <span class="section__badge">🤝 Partner Program</span>
                <h1 class="partner-auth-card__title">Registrasi Partner</h1>
                <p class="partner-auth-card__subtitle">Bergabunglah menjadi partner travine.id dan dapatkan komisi menarik!</p>
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

            <form method="POST" action="{{ route('partner.register') }}" class="partner-auth-form">
                @csrf
                <div class="partner-auth-form__group">
                    <label class="partner-auth-form__label" for="name">Nama Lengkap</label>
                    <input class="partner-auth-form__input" type="text" name="name" id="name" required value="{{ old('name') }}" placeholder="Masukkan nama lengkap">
                </div>
                <div class="partner-auth-form__group">
                    <label class="partner-auth-form__label" for="email">Email</label>
                    <input class="partner-auth-form__input" type="email" name="email" id="email" required value="{{ old('email') }}" placeholder="contoh@email.com">
                </div>
                <div class="partner-auth-form__group">
                    <label class="partner-auth-form__label" for="phone">No. WhatsApp</label>
                    <input class="partner-auth-form__input" type="text" name="phone" id="phone" required value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                </div>
                <div class="partner-auth-form__group">
                    <label class="partner-auth-form__label" for="password">Password</label>
                    <input class="partner-auth-form__input" type="password" name="password" id="password" required placeholder="Minimal 8 karakter">
                </div>
                <div class="partner-auth-form__group">
                    <label class="partner-auth-form__label" for="password_confirmation">Konfirmasi Password</label>
                    <input class="partner-auth-form__input" type="password" name="password_confirmation" id="password_confirmation" required placeholder="Ulangi password">
                </div>
                <button type="submit" class="btn btn--primary partner-auth-form__submit">
                    <span>Daftar Sekarang</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
            </form>

            <p class="partner-auth-card__footer">
                Sudah punya akun? <a href="{{ route('partner.login') }}" class="partner-auth-card__link">Login di sini</a>
            </p>
        </div>
    </div>
</section>
@endsection
