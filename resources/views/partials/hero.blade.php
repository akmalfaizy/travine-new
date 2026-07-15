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

    <div class="container hero__content">
        <div class="hero__grid">
            <div class="hero__text reveal">
                <span class="hero__badge">🌏 Indonesia's Leading Tourism Partner</span>
                <h1 class="hero__title">
                    Transforming Tourism with
                    <span class="hero__title-highlight">Authentic Experiences</span>
                </h1>
                <p class="hero__subtitle">
                    travine.id adalah mitra terpercaya dalam layanan manajemen perjalanan, pengembangan ekosistem pariwisata, konsultansi pengembangan dan operasional destinasi wisata di Indonesia. Kami menghadirkan pengalaman wisata
                    yang autentik, inovatif, dan berkelanjutan.
                </p>
                <div class="hero__buttons">
                    <a href="#services" class="btn btn--primary" id="hero-cta-services">
                        <span>Explore Services</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                    <a href="#contact" class="btn btn--outline" id="hero-cta-contact">
                        <span>Contact Us</span>
                    </a>
                </div>
            </div>

            <div class="hero__mascot reveal reveal--delay-1">
                <img src="{{ asset('images/maskot_travine.webp') }}" alt="Maskot Travine" class="hero__mascot-img">
            </div>
        </div>

        {{-- Stats --}}
        <!-- <div class="hero__stats reveal reveal--delay-2">
            <div class="hero__stat">
                <span class="hero__stat-number">150+</span>
                <span class="hero__stat-label">Destinasi Wisata</span>
            </div>
            <div class="hero__stat">
                <span class="hero__stat-number">50+</span>
                <span class="hero__stat-label">Proyek Ekosistem</span>
            </div>
            <div class="hero__stat">
                <span class="hero__stat-number">10K+</span>
                <span class="hero__stat-label">Happy Travelers</span>
            </div>
        </div> -->
    </div>
</section>
