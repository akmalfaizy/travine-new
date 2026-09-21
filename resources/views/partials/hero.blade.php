{{-- Hero Section --}}
<section class="hero" id="hero">
    {{-- Animated background shapes --}}
    <div class="hero__shapes">
        <div class="hero__shape hero__shape--1"></div>
        <div class="hero__shape hero__shape--2"></div>
        <div class="hero__shape hero__shape--3"></div>
        <div class="hero__shape hero__shape--4"></div>
        <div class="hero__shape hero__shape--5"></div>
        {{-- Animated plane --}}
        <svg class="hero__plane" viewBox="0 0 64 64" fill="none">
            <path d="M4 36L28 32L56 8L32 28L28 32L4 36Z" fill="rgba(255,255,255,0.12)"/>
            <path d="M28 32L24 52L32 40L28 32Z" fill="rgba(255,255,255,0.08)"/>
        </svg>
    </div>

    {{-- Wave divider --}}
    <div class="hero__wave">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path d="M0,60 C360,120 720,0 1080,60 C1260,90 1380,80 1440,60 L1440,120 L0,120 Z" fill="var(--bg-main)"/>
        </svg>
    </div>

    <div class="hero__content">
        <div class="hero__grid">
            <div class="hero__text reveal">
                <span class="hero__badge">🌏 Indonesia's Leading Tourism Partner</span>
                <h1 class="hero__title">
                    Transforming Tourism with
                    <span class="hero__title-highlight">Authentic Experiences</span>
                </h1>
                <p class="hero__subtitle">
                    {{ __('travine.id adalah partner terpercaya dalam layanan manajemen perjalanan, pengembangan ekosistem pariwisata, konsultansi pengembangan dan operasional destinasi wisata di Indonesia. Kami menghadirkan pengalaman wisata yang autentik, inovatif, dan berkelanjutan.') }}
                </p>
                <div class="hero__buttons">
                    <a href="#services" class="btn btn--primary" id="hero-cta-services">
                        <span>{{ __('Explore Services') }}</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                    <a href="#contact" class="btn btn--outline" id="hero-cta-contact">
                        <span>{{ __('Contact Us') }}</span>
                    </a>
                    <a href="{{ route('bookings.track') }}" class="btn btn--ghost" id="hero-cta-track" style="display:inline-flex; align-items:center; gap:8px; font-weight:600; font-size:0.88rem; padding:10px 18px; border-radius:10px; border:1.5px dashed rgba(255,255,255,0.45); color:rgba(255,255,255,0.88); text-decoration:none; transition:all 0.25s ease; backdrop-filter:blur(4px);" onmouseover="this.style.borderColor='rgba(255,255,255,0.85)'; this.style.color='#fff'; this.style.background='rgba(255,255,255,0.1)';" onmouseout="this.style.borderColor='rgba(255,255,255,0.45)'; this.style.color='rgba(255,255,255,0.88)'; this.style.background='transparent';">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <span>{{ __('Cek Status Booking') }}</span>
                    </a>
                </div>
            </div>

            <div class="partner-cta reveal reveal--delay-1" style="text-align:center;">
                <h2 class="text-gradient" style="font-size:2rem; margin-bottom:1rem;">{{ __('Ayo menjadi partner kami dan dapatkan komisi!') }}</h2>
                <a href="{{ route('partner.register') }}" class="btn btn--primary">{{ __('Daftar Sekarang') }}</a>
            </div>
        </div>
    </div>
</section>
