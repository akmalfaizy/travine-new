{{-- Services Section --}}
<section class="services section" id="services">
    <div class="container">
        <div class="section__header reveal">
            <span class="section__badge">Layanan Kami</span>
            <h2 class="section__title">Solusi Wisata <span class="text-gradient">Komprehensif</span></h2>
            <p class="section__subtitle">Kami menyediakan layanan lengkap untuk memenuhi kebutuhan wisata dan pengembangan destinasi Anda.</p>
        </div>

        <div class="services__grid">
            {{-- Service 1: Jasa Wisata --}}
            <div class="services__card reveal" id="service-wisata">
                <div class="services__card-icon">
                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                        <circle cx="24" cy="24" r="22" stroke="url(#svcGrad1)" stroke-width="1.5" fill="rgba(20,184,166,0.08)"/>
                        <path d="M16 32 L24 14 L32 32" stroke="#14B8A6" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18 28 H30" stroke="#14B8A6" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="24" cy="14" r="2" fill="#F97316"/>
                        <path d="M12 34 C16 30, 32 30, 36 34" stroke="#0F2A52" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                        <defs>
                            <linearGradient id="svcGrad1" x1="0" y1="0" x2="48" y2="48">
                                <stop offset="0%" stop-color="#14B8A6"/>
                                <stop offset="100%" stop-color="#0F2A52"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <h3 class="services__card-title">Jasa Wisata</h3>
                <p class="services__card-desc">
                   Perencanaan dan pengelolaan paket wisata sesuai kebutuhan yang menghadirkan pengalaman autentik. Custom itinerary, group travel, dan private tour di seluruh destinasi Indonesia.
                </p>
                <ul class="services__card-features">
                    <li>Custom Itinerary</li>
                    <li>Group & Private Tour</li>
                    <li>Local Experience</li>
                </ul>
                <a href="#contact" class="services__card-link">
                    Pelajari Lebih Lanjut
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>

            {{-- Service 2: Pengembangan Ekosistem --}}
            <div class="services__card services__card--featured reveal reveal--delay-1" id="service-ekosistem">
                <div class="services__card-badge">Popular</div>
                <div class="services__card-icon">
                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                        <circle cx="24" cy="24" r="22" stroke="url(#svcGrad2)" stroke-width="1.5" fill="rgba(20,184,166,0.08)"/>
                        <circle cx="24" cy="18" r="6" stroke="#14B8A6" stroke-width="2" fill="none"/>
                        <circle cx="14" cy="32" r="4" stroke="#14B8A6" stroke-width="1.5" fill="none"/>
                        <circle cx="34" cy="32" r="4" stroke="#14B8A6" stroke-width="1.5" fill="none"/>
                        <path d="M24 24 L14 28" stroke="#F97316" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M24 24 L34 28" stroke="#F97316" stroke-width="1.5" stroke-linecap="round"/>
                        <defs>
                            <linearGradient id="svcGrad2" x1="0" y1="0" x2="48" y2="48">
                                <stop offset="0%" stop-color="#14B8A6"/>
                                <stop offset="100%" stop-color="#0F2A52"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <h3 class="services__card-title">Pengembangan Ekosistem Wisata</h3>
                <p class="services__card-desc">
                    Membangun ekosistem wisata terintegrasi yang menghubungkan pelaku usaha lokal,
                    pemerintah, dan komunitas untuk menciptakan destinasi wisata berkelanjutan.
                </p>
                <ul class="services__card-features">
                    <li>Ecosystem Mapping</li>
                    <li>Stakeholder Integration</li>
                    <li>Sustainable Development</li>
                </ul>
                <a href="#contact" class="services__card-link">
                    Pelajari Lebih Lanjut
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>

            {{-- Service 3: Konsultan --}}
            <div class="services__card reveal reveal--delay-2" id="service-konsultan">
                <div class="services__card-icon">
                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                        <circle cx="24" cy="24" r="22" stroke="url(#svcGrad3)" stroke-width="1.5" fill="rgba(20,184,166,0.08)"/>
                        <rect x="14" y="12" width="20" height="24" rx="3" stroke="#14B8A6" stroke-width="2" fill="none"/>
                        <path d="M19 20 H29" stroke="#14B8A6" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M19 25 H29" stroke="#14B8A6" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M19 30 H25" stroke="#14B8A6" stroke-width="1.5" stroke-linecap="round"/>
                        <circle cx="35" cy="35" r="6" fill="#0B1D3A" stroke="#F97316" stroke-width="1.5"/>
                        <path d="M33 35 L35 37 L38 33" stroke="#F97316" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <defs>
                            <linearGradient id="svcGrad3" x1="0" y1="0" x2="48" y2="48">
                                <stop offset="0%" stop-color="#14B8A6"/>
                                <stop offset="100%" stop-color="#0F2A52"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <h3 class="services__card-title">Konsultan Tempat Wisata</h3>
                <p class="services__card-desc">
                    Konsultasi profesional untuk pengembangan, branding, dan optimalisasi
                    destinasi wisata. Dari feasibility study hingga implementasi strategi pemasaran.
                </p>
                <ul class="services__card-features">
                    <li>Feasibility Study</li>
                    <li>Destination Branding</li>
                    <li>Marketing Strategy</li>
                </ul>
                <a href="#contact" class="services__card-link">
                    Pelajari Lebih Lanjut
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>
