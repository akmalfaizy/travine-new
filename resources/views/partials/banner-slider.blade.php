{{-- Promo & Open Trip Banner Slider Section --}}
@if(isset($banners) && $banners->count() > 0)
<section class="banner-slider-section" id="promo-banners">
    <div class="container">
        <div class="banner-slider-wrapper">
            <div class="banner-carousel" id="travineBannerCarousel">
                @foreach($banners as $index => $banner)
                    <div class="banner-slide {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
                        <a href="{{ $banner->target_url }}" class="banner-slide__link" title="{{ $banner->title }}">
                            {{-- Picture element for responsive desktop/mobile images --}}
                            <picture class="banner-slide__picture">
                                @if($banner->mobile_image)
                                    <source media="(max-width: 768px)" srcset="{{ $banner->mobile_image_url }}">
                                @endif
                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="banner-slide__img" loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                            </picture>

                            {{-- Gradient Overlay with Info --}}
                            <div class="banner-slide__overlay">
                                <div class="banner-slide__content">
                                    @if($banner->tag)
                                        <span class="banner-slide__badge">
                                            {{ $banner->tag }}
                                        </span>
                                    @endif

                                    <h3 class="banner-slide__title">
                                        {{ $banner->title }}
                                    </h3>

                                    @if($banner->subtitle)
                                        <p class="banner-slide__subtitle">
                                            {{ $banner->subtitle }}
                                        </p>
                                    @endif

                                    @if($banner->tour)
                                        <div class="banner-slide__tour-badge">
                                            <span style="font-size:0.85rem;">💰 Mulai</span>
                                            <strong style="color:#ffffff; font-size:1.05rem;">{{ $banner->tour->price_formatted }}</strong>
                                        </div>
                                    @endif

                                    <div class="banner-slide__cta">
                                        <span class="btn btn--primary btn--sm banner-slide__btn">
                                            {{ $banner->button_text ?: 'Lihat Paket' }}
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            @if($banners->count() > 1)
                {{-- Navigation Buttons --}}
                <button class="banner-nav-btn banner-nav-btn--prev" id="bannerPrevBtn" aria-label="Slide Sebelumnya">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                </button>
                <button class="banner-nav-btn banner-nav-btn--next" id="bannerNextBtn" aria-label="Slide Selanjutnya">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                </button>

                {{-- Dots Indicators --}}
                <div class="banner-dots" id="bannerDots">
                    @foreach($banners as $index => $banner)
                        <button class="banner-dot {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}" aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
@endif

{{-- Welcome Promo Pop-up Modal (If enabled for a banner) --}}
@if(isset($popupBanner) && $popupBanner)
<div class="promo-modal-backdrop" id="promoPopupModal" style="display:none;">
    <div class="promo-modal-card">
        <button class="promo-modal-close" id="closePromoModal" aria-label="Tutup Pop-up">&times;</button>
        <a href="{{ $popupBanner->target_url }}" class="promo-modal-link">
            <div class="promo-modal-img-wrap">
                <img src="{{ $popupBanner->image_url }}" alt="{{ $popupBanner->title }}">
                @if($popupBanner->tag)
                    <span class="promo-modal-tag">{{ $popupBanner->tag }}</span>
                @endif
            </div>
            <div class="promo-modal-body">
                <h3 class="promo-modal-title">{{ $popupBanner->title }}</h3>
                @if($popupBanner->subtitle)
                    <p class="promo-modal-desc">{{ $popupBanner->subtitle }}</p>
                @endif
                <div class="promo-modal-action">
                    <span class="btn btn--primary" style="width:100%; justify-content:center; padding:12px; font-weight:700;">
                        {{ $popupBanner->button_text ?: 'Pesan Sekarang' }} 🚀
                    </span>
                </div>
            </div>
        </a>
    </div>
</div>
@endif

<style>
/* Banner Slider Section Styles */
.banner-slider-section {
    padding: 30px 0 20px;
    position: relative;
    z-index: 10;
}
.banner-slider-wrapper {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 16px 40px rgba(0,0,0,0.22);
    border: 1px solid var(--glass-border);
    background: #0f172a;
}
.banner-carousel {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 9; /* Rasio Desktop 16:9 */
    overflow: hidden;
}
.banner-slide {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1), transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    transform: scale(1.02);
    z-index: 1;
    pointer-events: none;
}
.banner-slide.active {
    position: absolute;
    opacity: 1;
    visibility: visible;
    transform: scale(1);
    z-index: 2;
    pointer-events: auto;
}
.banner-slide__link {
    display: block;
    position: relative;
    text-decoration: none;
    width: 100%;
    height: 100%;
    color: inherit;
}
.banner-slide__picture {
    display: block;
    width: 100%;
    height: 100%;
}
.banner-slide__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.banner-slide__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, rgba(15,23,42,0.88) 0%, rgba(15,23,42,0.6) 45%, rgba(15,23,42,0.1) 100%);
    display: flex;
    align-items: center;
    padding: 36px 48px;
}
.banner-slide__content {
    max-width: 580px;
}
.banner-slide__badge {
    display: inline-block;
    padding: 5px 14px;
    background: linear-gradient(135deg, var(--teal), #06b6d4);
    color: #ffffff;
    font-size: 0.78rem;
    font-weight: 800;
    border-radius: 30px;
    letter-spacing: 0.04em;
    margin-bottom: 12px;
    box-shadow: 0 4px 15px rgba(20,184,166,0.4);
    text-transform: uppercase;
}
.banner-slide__title {
    font-size: 1.85rem;
    font-weight: 850;
    color: #ffffff;
    line-height: 1.2;
    margin-bottom: 8px;
    text-shadow: 0 2px 8px rgba(0,0,0,0.5);
}
.banner-slide__subtitle {
    font-size: 0.95rem;
    color: #e2e8f0;
    line-height: 1.5;
    margin-bottom: 14px;
    text-shadow: 0 1px 4px rgba(0,0,0,0.4);
}
.banner-slide__tour-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    padding: 6px 14px;
    border-radius: 8px;
    margin-bottom: 16px;
    color: #cbd5e1;
}
.banner-slide__cta {
    margin-top: 6px;
}
.banner-slide__btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    padding: 10px 22px;
    font-size: 0.9rem;
    border-radius: 10px;
}

/* Navigation Buttons */
.banner-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: rgba(15,23,42,0.65);
    border: 1px solid rgba(255,255,255,0.2);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    backdrop-filter: blur(8px);
    transition: all 0.25s ease;
    z-index: 5;
}
.banner-nav-btn:hover {
    background: var(--teal);
    border-color: var(--teal);
    transform: translateY(-50%) scale(1.08);
}
.banner-nav-btn--prev { left: 16px; }
.banner-nav-btn--next { right: 16px; }

/* Dots */
.banner-dots {
    position: absolute;
    bottom: 16px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 8px;
    z-index: 5;
}
.banner-dot {
    width: 28px;
    height: 5px;
    border-radius: 4px;
    background: rgba(255,255,255,0.3);
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 0;
}
.banner-dot.active {
    background: var(--teal);
    width: 44px;
}

/* Pop-up Modal */
.promo-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.75);
    backdrop-filter: blur(6px);
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.promo-modal-card {
    background: var(--bg-main);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    width: 100%;
    max-width: 460px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 25px 60px rgba(0,0,0,0.5);
    animation: modalPop 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes modalPop {
    from { opacity: 0; transform: scale(0.9) translateY(20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
.promo-modal-close {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: rgba(0,0,0,0.65);
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
    font-size: 1.4rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    line-height: 1;
}
.promo-modal-link {
    text-decoration: none;
    color: inherit;
    display: block;
}
.promo-modal-img-wrap {
    position: relative;
    height: 220px;
    background: #000;
}
.promo-modal-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.promo-modal-tag {
    position: absolute;
    bottom: 12px;
    left: 14px;
    background: linear-gradient(135deg, var(--teal), #06b6d4);
    color: #fff;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
}
.promo-modal-body {
    padding: 22px 24px;
}
.promo-modal-title {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 6px;
    line-height: 1.3;
}
.promo-modal-desc {
    font-size: 0.875rem;
    color: var(--text-muted);
    line-height: 1.5;
    margin-bottom: 18px;
}

/* Mobile Responsiveness - Feed Instagram 1080x1080 (1:1 Ratio) */
@media (max-width: 768px) {
    .banner-slider-section {
        padding: 16px 0 12px;
    }
    .banner-slider-wrapper {
        border-radius: 16px;
    }
    .banner-carousel {
        aspect-ratio: 1 / 1; /* Feed Instagram 1080x1080 */
    }
    .banner-slide__picture,
    .banner-slide__img {
        width: 100%;
        height: 100%;
        aspect-ratio: 1 / 1;
        object-fit: cover;
    }
    .banner-slide__overlay {
        padding: 20px 18px 26px;
        background: linear-gradient(180deg, rgba(15,23,42,0) 0%, rgba(15,23,42,0.15) 30%, rgba(15,23,42,0.7) 65%, rgba(15,23,42,0.95) 100%);
        align-items: flex-end;
    }
    .banner-slide__content {
        max-width: 100%;
    }
    .banner-slide__badge {
        font-size: 0.7rem;
        padding: 4px 10px;
        margin-bottom: 6px;
    }
    .banner-slide__title {
        font-size: 1.25rem;
        line-height: 1.25;
        margin-bottom: 6px;
    }
    .banner-slide__subtitle {
        font-size: 0.8rem;
        line-height: 1.35;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .banner-slide__tour-badge {
        padding: 4px 12px;
        font-size: 0.8rem;
        margin-bottom: 10px;
    }
    .banner-slide__btn {
        padding: 8px 18px;
        font-size: 0.85rem;
    }
    .banner-nav-btn {
        display: none; /* Navigasi swipe touch di mobile */
    }
    .banner-dots {
        bottom: 8px;
        gap: 6px;
    }
    .banner-dot {
        width: 18px;
        height: 4px;
    }
    .banner-dot.active {
        width: 32px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Banner Carousel Logic
    const carousel = document.getElementById('travineBannerCarousel');
    if (carousel) {
        const slides = carousel.querySelectorAll('.banner-slide');
        const dots = document.querySelectorAll('.banner-dot');
        const prevBtn = document.getElementById('bannerPrevBtn');
        const nextBtn = document.getElementById('bannerNextBtn');
        let currentSlide = 0;
        let slideInterval = null;

        function goToSlide(n) {
            slides[currentSlide].classList.remove('active');
            if (dots.length > 0) dots[currentSlide].classList.remove('active');

            currentSlide = (n + slides.length) % slides.length;

            slides[currentSlide].classList.add('active');
            if (dots.length > 0) dots[currentSlide].classList.add('active');
        }

        function nextSlide() {
            goToSlide(currentSlide + 1);
        }

        function prevSlide() {
            goToSlide(currentSlide - 1);
        }

        function startAutoPlay() {
            if (slides.length > 1) {
                slideInterval = setInterval(nextSlide, 5000);
            }
        }

        function stopAutoPlay() {
            if (slideInterval) {
                clearInterval(slideInterval);
            }
        }

        if (nextBtn) nextBtn.addEventListener('click', function(e) { e.preventDefault(); nextSlide(); stopAutoPlay(); startAutoPlay(); });
        if (prevBtn) prevBtn.addEventListener('click', function(e) { e.preventDefault(); prevSlide(); stopAutoPlay(); startAutoPlay(); });

        dots.forEach((dot, idx) => {
            dot.addEventListener('click', function(e) {
                e.preventDefault();
                goToSlide(idx);
                stopAutoPlay();
                startAutoPlay();
            });
        });

        // Pause on hover
        const wrapper = carousel.closest('.banner-slider-wrapper');
        if (wrapper) {
            wrapper.addEventListener('mouseenter', stopAutoPlay);
            wrapper.addEventListener('mouseleave', startAutoPlay);

            // Touch Swipe support for mobile
            let touchStartX = 0;
            let touchEndX = 0;

            wrapper.addEventListener('touchstart', function(e) {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });

            wrapper.addEventListener('touchend', function(e) {
                touchEndX = e.changedTouches[0].screenX;
                if (touchStartX - touchEndX > 50) {
                    nextSlide(); // swipe left -> next
                } else if (touchEndX - touchStartX > 50) {
                    prevSlide(); // swipe right -> prev
                }
            }, { passive: true });
        }

        startAutoPlay();
    }

    // 2. Pop-up Modal (Shows once per browser session)
    const popupModal = document.getElementById('promoPopupModal');
    const closeBtn = document.getElementById('closePromoModal');

    if (popupModal && closeBtn) {
        const hasSeenPopup = sessionStorage.getItem('travine_promo_popup_seen');
        if (!hasSeenPopup) {
            setTimeout(function () {
                popupModal.style.display = 'flex';
                sessionStorage.setItem('travine_promo_popup_seen', 'true');
            }, 2500);
        }

        closeBtn.addEventListener('click', function () {
            popupModal.style.display = 'none';
        });

        popupModal.addEventListener('click', function (e) {
            if (e.target === popupModal) {
                popupModal.style.display = 'none';
            }
        });
    }
});
</script>
