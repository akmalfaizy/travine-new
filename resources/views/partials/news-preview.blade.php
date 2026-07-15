{{-- Berita Terbaru Section --}}
<section class="section news-preview-section" id="berita-terbaru" style="padding: 80px 0;">
    <div class="container">
        <div class="section__header reveal" style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 20px; margin-bottom: 40px;">
            <div>
                <span class="section__badge">Kabar Terbaru</span>
                <h2 class="section__title" style="margin-bottom: 0;">Artikel & <span class="text-gradient">Berita</span></h2>
            </div>
            <a href="{{ route('news.index') }}" class="btn btn--secondary" style="text-decoration: none;">
                Lihat Semua Artikel
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left: 8px;">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="news-preview__grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px;">
            @forelse($latestNews as $newsItem)
                <article class="news-card reveal" style="background: var(--card-bg, #ffffff); border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: transform 0.3s ease, box-shadow 0.3s ease; display: flex; flex-direction: column; height: 100%;">
                    <div class="news-card__img-wrapper" style="position: relative; height: 220px; overflow: hidden;">
                        <img src="{{ $newsItem->thumbnail ? asset('storage/' . $newsItem->thumbnail) : asset('images/hero_bg.jpg') }}" alt="{{ $newsItem->title }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                        <span class="news-card__badge" style="position: absolute; top: 20px; left: 20px; background: var(--teal, #14B8A6); color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                            {{ $newsItem->author }}
                        </span>
                    </div>
                    <div class="news-card__body" style="padding: 24px; display: flex; flex-direction: column; flex-grow: 1;">
                        <div class="news-card__meta" style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <span>{{ $newsItem->published_at ? \Carbon\Carbon::parse($newsItem->published_at)->translatedFormat('d M Y') : $newsItem->created_at->translatedFormat('d M Y') }}</span>
                        </div>
                        <h3 class="news-card__title" style="font-size: 1.25rem; font-weight: 700; margin-bottom: 12px; line-height: 1.4;">
                            <a href="{{ route('news.show', $newsItem->slug) }}" style="color: inherit; text-decoration: none; transition: color 0.3s;">
                                {{ Str::limit($newsItem->title, 60) }}
                            </a>
                        </h3>
                        <p class="news-card__summary" style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px; flex-grow: 1;">
                            {{ Str::limit(strip_tags($newsItem->summary), 120) }}
                        </p>
                        <a href="{{ route('news.show', $newsItem->slug) }}" class="news-card__link" style="font-weight: 600; color: var(--teal, #14B8A6); text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 0.95rem; transition: gap 0.3s;">
                            Baca Artikel
                            <svg class="news-card__arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </article>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--text-muted);">
                    <p>Belum ada artikel atau berita terbaru saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
