{{-- Kategori Wisata Section --}}
<section class="section categories-section" id="kategori-wisata" style="padding: 80px 0; background: var(--bg-offset, #f8fafc);">
    <div class="container">
        <div class="section__header reveal">
            <span class="section__badge">Destinasi</span>
            <h2 class="section__title">Kategori Wisata <span class="text-gradient">Populer</span></h2>
            <p class="section__subtitle">Temukan petualangan Anda berikutnya berdasarkan jenis destinasi dan aktivitas favorit.</p>
        </div>

        <div class="categories__grid" style="display: flex; justify-content: center; flex-wrap: wrap; gap: 24px; margin-top: 40px;">
            @forelse($categories as $category)
                <a href="{{ route('tours.category', $category->slug) }}" class="category-card reveal" style="text-decoration: none; width: 320px; max-width: 100%;">
                    <div class="category-card__inner">
                        <div class="category-card__icon-wrapper">
                            <span class="category-card__emoji">{{ $category->icon ?: '🌴' }}</span>
                        </div>
                        <h3 class="category-card__title">{{ $category->name }}</h3>
                        <p class="category-card__count">{{ $category->active_tours_count ?? 0 }} Paket Wisata</p>
                        <span class="category-card__link-text">
                            Lihat Paket
                            <svg class="category-card__arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </span>
                    </div>
                </a>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--text-muted);">
                    <p>Belum ada kategori wisata aktif.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
