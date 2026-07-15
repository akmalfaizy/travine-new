{{-- About Section --}}
<section class="about section" id="about">
    <div class="container">
        <div class="about__grid">
            {{-- Visual side --}}
            <div class="about__visual reveal">
                <div class="about__image-wrapper">
                    <div class="about__image-card about__image-card--1">
                        <svg viewBox="0 0 200 200" fill="none">
                            <rect rx="16" width="200" height="200" fill="url(#aboutGrad1)"/>
                            <circle cx="100" cy="80" r="35" fill="rgba(255,255,255,0.15)"/>
                            <path d="M40 140 C60 100, 140 100, 160 140 L160 180 C160 190 152 200 140 200 L60 200 C48 200 40 190 40 180 Z" fill="rgba(255,255,255,0.1)"/>
                            <path d="M80 65 L100 45 L120 65" stroke="rgba(255,255,255,0.4)" stroke-width="2" fill="none" stroke-linecap="round"/>
                            <circle cx="60" cy="40" r="4" fill="#F97316" opacity="0.6"/>
                            <circle cx="150" cy="50" r="3" fill="#14B8A6" opacity="0.6"/>
                            <defs>
                                <linearGradient id="aboutGrad1" x1="0" y1="0" x2="200" y2="200">
                                    <stop offset="0%" stop-color="#0F2A52"/>
                                    <stop offset="100%" stop-color="#14B8A6"/>
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                    <div class="about__image-card about__image-card--2">
                        <svg viewBox="0 0 160 160" fill="none">
                            <rect rx="12" width="160" height="160" fill="url(#aboutGrad2)"/>
                            <path d="M40 120 L80 60 L120 120 Z" fill="rgba(255,255,255,0.12)" stroke="rgba(255,255,255,0.2)" stroke-width="1"/>
                            <circle cx="80" cy="55" r="8" fill="#F97316" opacity="0.5"/>
                            <path d="M30 110 C50 90, 80 100, 100 85 C120 70, 130 80, 140 110" stroke="rgba(255,255,255,0.2)" stroke-width="1.5" fill="none"/>
                            <defs>
                                <linearGradient id="aboutGrad2" x1="0" y1="0" x2="160" y2="160">
                                    <stop offset="0%" stop-color="#14B8A6"/>
                                    <stop offset="100%" stop-color="#0B1D3A"/>
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                    {{-- Floating badge --}}
                    <div class="about__floating-badge">
                        <span class="about__floating-number">8+</span>
                        <span class="about__floating-text">Tahun Pengalaman</span>
                    </div>
                </div>
            </div>

            {{-- Text side --}}
            <div class="about__content reveal reveal--delay-1">
                <span class="section__badge">Tentang Kami</span>
                <h2 class="section__title">
                    Membangun Ekosistem Wisata yang
                    <span class="text-gradient">Berkelanjutan</span>
                </h2>
                <p class="about__description">
                    travine.id didirikan dengan visi untuk mentransformasi industri pariwisata Indonesia
                    melalui pendekatan yang inovatif dan berkelanjutan. Kami percaya bahwa setiap destinasi
                    memiliki potensi unik yang dapat dikembangkan menjadi pengalaman wisata yang autentik
                    dan berkesan.
                </p>
                <p class="about__description">
                    Dengan tim ahli yang berpengalaman di bidang pariwisata, pengembangan destinasi,
                    dan manajemen ekosistem wisata, kami menghadirkan solusi komprehensif yang
                    menghubungkan potensi lokal dengan standar kualitas internasional.
                </p>

                <div class="about__highlights">
                    <div class="about__highlight">
                        <div class="about__highlight-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div>
                            <h4>Terpercaya</h4>
                            <p>Dikelola oleh para professional yang kompetances dibidangnya</p>
                        </div>
                    </div>
                    <div class="about__highlight">
                        <div class="about__highlight-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        </div>
                        <div>
                            <h4>Berpengalaman</h4>
                            <p>8+ tahun pengalaman di operator pariwisata & pengembangan kawasan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
