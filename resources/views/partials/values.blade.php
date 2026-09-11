{{-- Core Values Section --}}
<section class="values section section--alt" id="values">
    <div class="container">
        <div class="section__header reveal">
            <span class="section__badge">{{ __('Nilai Kami') }}</span>
            <h2 class="section__title">Core <span class="text-gradient">Values</span></h2>
            <p class="section__subtitle">{{ __('Nilai-nilai yang menjadi fondasi setiap layanan dan proyek yang kami kerjakan.') }}</p>
        </div>

        <div class="values__grid">
            {{-- Value 1: Authentic --}}
            <div class="values__card reveal" id="value-authentic">
                <div class="values__card-glow"></div>
                <div class="values__card-icon">
                    <svg width="56" height="56" viewBox="0 0 56 56" fill="none">
                        <circle cx="28" cy="28" r="26" fill="rgba(20,184,166,0.06)" stroke="rgba(20,184,166,0.2)" stroke-width="1"/>
                        <path d="M28 16 L32 24 L40 25 L34 31 L35.5 39 L28 35 L20.5 39 L22 31 L16 25 L24 24 Z" stroke="#14B8A6" stroke-width="2" fill="rgba(20,184,166,0.15)" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3 class="values__card-title">Authentic</h3>
                <p class="values__card-desc">
                    {{ __('Menghadirkan pengalaman wisata yang genuine dan otentik, menghargai kearifan lokal dan keunikan setiap destinasi.') }}
                </p>
            </div>

            {{-- Value 2: Flexible & Transparent --}}
            <div class="values__card reveal reveal--delay-1" id="value-flexible">
                <div class="values__card-glow"></div>
                <div class="values__card-icon">
                    <svg width="56" height="56" viewBox="0 0 56 56" fill="none">
                        <circle cx="28" cy="28" r="26" fill="rgba(20,184,166,0.06)" stroke="rgba(20,184,166,0.2)" stroke-width="1"/>
                        <rect x="16" y="20" width="24" height="16" rx="3" stroke="#14B8A6" stroke-width="2" fill="none"/>
                        <path d="M16 26 H40" stroke="#14B8A6" stroke-width="1.5"/>
                        <circle cx="22" cy="32" r="2" fill="#F97316" opacity="0.7"/>
                        <path d="M28 32 H36" stroke="rgba(20,184,166,0.5)" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <h3 class="values__card-title">Flexible & Transparent</h3>
                <p class="values__card-desc">
                    {{ __('Layanan yang adaptif sesuai kebutuhan klien dengan proses yang transparan, terbuka, dan dapat dipercaya di setiap tahapan kerja sama.') }}
                </p>
            </div>

            {{-- Value 3: Full Services --}}
            <div class="values__card reveal reveal--delay-2" id="value-fullservice">
                <div class="values__card-glow"></div>
                <div class="values__card-icon">
                    <svg width="56" height="56" viewBox="0 0 56 56" fill="none">
                        <circle cx="28" cy="28" r="26" fill="rgba(20,184,166,0.06)" stroke="rgba(20,184,166,0.2)" stroke-width="1"/>
                        <circle cx="28" cy="22" r="6" stroke="#14B8A6" stroke-width="2" fill="none"/>
                        <circle cx="18" cy="36" r="4" stroke="#14B8A6" stroke-width="1.5" fill="none"/>
                        <circle cx="38" cy="36" r="4" stroke="#14B8A6" stroke-width="1.5" fill="none"/>
                        <path d="M28 28 L18 32" stroke="#F97316" stroke-width="1.5" opacity="0.6"/>
                        <path d="M28 28 L38 32" stroke="#F97316" stroke-width="1.5" opacity="0.6"/>
                        <path d="M18 40 L38 40" stroke="rgba(20,184,166,0.3)" stroke-width="1" stroke-dasharray="3 3"/>
                    </svg>
                </div>
                <h3 class="values__card-title">Full Services</h3>
                <p class="values__card-desc">
                    {{ __('Solusi end-to-end dari perencanaan, pengembangan, hingga pengelolaan — semua kebutuhan wisata Anda terlayani dalam satu ekosistem terintegrasi.') }}
                </p>
            </div>
        </div>
    </div>
</section>
