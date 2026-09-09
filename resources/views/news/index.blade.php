@extends('layouts.app')

@section('content')
<div class="news-header" style="background: linear-gradient(135deg, #0F2A52 0%, #061124 100%); padding: 120px 0 80px; text-align: center; color: #fff; position: relative; overflow: hidden;">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.1; pointer-events: none;">
        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#fff" stroke-width="1"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)" />
        </svg>
    </div>
    <div class="container" style="position: relative; z-index: 1;">
        <span class="section__badge" style="background: rgba(20,184,166,0.15); color: var(--teal, #14B8A6); margin-bottom: 12px; display: inline-block;">Blog & Berita</span>
        <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 16px;">Kabar & Artikel <span class="text-gradient">Terbaru</span></h1>
        <p style="font-size: 1.15rem; max-width: 600px; margin: 0 auto; color: rgba(255,255,255,0.7); line-height: 1.6;">
            Temukan inspirasi perjalanan, tips liburan, dan berita terbaru seputar destinasi pariwisata di Indonesia.
        </p>
    </div>
</div>

<div class="news-body" style="padding: 80px 0; background: var(--bg-main);">
    <div class="container">
        @if($news->count() > 0)
            <div class="news-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
                @foreach($news as $item)
                    <article class="news-card" style="background: var(--card-bg, #ffffff); border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: transform 0.3s ease, box-shadow 0.3s ease; display: flex; flex-direction: column; height: 100%;">
                        <div class="news-card__img-wrapper" style="position: relative; height: 220px; overflow: hidden;">
                            <img src="{{ $item->thumbnail ? asset('storage/' . $item->thumbnail) : asset('images/hero_bg.jpg') }}" alt="{{ $item->title }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                            <span class="news-card__badge" style="position: absolute; top: 20px; left: 20px; background: var(--teal, #14B8A6); color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                {{ $item->author }}
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
                                <span>{{ $item->published_at ? $item->published_at->translatedFormat('d M Y') : $item->created_at->translatedFormat('d M Y') }}</span>
                            </div>
                            <h3 class="news-card__title" style="font-size: 1.25rem; font-weight: 700; margin-bottom: 12px; line-height: 1.4;">
                                <a href="{{ route('news.show', $item->slug) }}" style="color: inherit; text-decoration: none; transition: color 0.3s;">
                                    {{ Str::limit($item->title, 60) }}
                                </a>
                            </h3>
                            <p class="news-card__summary" style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px; flex-grow: 1;">
                                {{ Str::limit(strip_tags($item->summary), 120) }}
                            </p>
                            <a href="{{ route('news.show', $item->slug) }}" class="news-card__link" style="font-weight: 600; color: var(--teal, #14B8A6); text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 0.95rem; transition: gap 0.3s; margin-top: auto;">
                                Baca Artikel
                                <svg class="news-card__arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="pagination-wrapper" style="margin-top: 50px; display: flex; justify-content: center;">
                {{ $news->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 80px 20px; background: var(--card-bg, #ffffff); border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
                <div style="font-size: 4rem; margin-bottom: 20px;">📰</div>
                <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 8px;">Belum Ada Artikel</h3>
                <p style="color: var(--text-muted); max-width: 400px; margin: 0 auto 24px;">Tim penulis kami sedang menyiapkan artikel menarik untuk Anda. Kembali lagi beberapa saat lagi!</p>
                <a href="{{ route('home') }}" class="btn btn--primary" style="text-decoration: none;">Kembali ke Beranda</a>
            </div>
        @endif
    </div>
</div>
@endsection
