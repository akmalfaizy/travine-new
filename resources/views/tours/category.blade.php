@extends('layouts.app')

@section('content')
<div class="category-header" style="background: linear-gradient(135deg, #0F2A52 0%, #061124 100%); padding: 120px 0 80px; text-align: center; color: #fff; position: relative; overflow: hidden;">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.1; pointer-events: none;">
        {{-- Vector pattern or subtle texture background --}}
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
        <span class="section__badge" style="background: rgba(20,184,166,0.15); color: var(--teal, #14B8A6); margin-bottom: 12px; display: inline-block;">{{ __('Kategori Wisata') }}</span>
        <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 16px;">
            <span style="font-size: 1.5em; vertical-align: middle; margin-right: 8px;">{{ $category->icon ?: '🌴' }}</span>{{ $category->name }}
        </h1>
        <p style="font-size: 1.15rem; max-width: 600px; margin: 0 auto; color: rgba(255,255,255,0.7); line-height: 1.6;">
            {{ __('Jelajahi petualangan menakjubkan dan paket wisata pilihan terbaik kami di kategori') }} {{ $category->name }}.
        </p>
    </div>
</div>

<div class="category-body" style="padding: 80px 0; background: var(--bg-main);">
    <div class="container">
        @if($tours->count() > 0)
            <div class="tours-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
                @foreach($tours as $tour)
                    <div class="tour-card" style="background: var(--card-bg); border: 1px solid var(--glass-border); border-radius: 16px; overflow: hidden; box-shadow: var(--shadow); transition: transform 0.3s ease, box-shadow 0.3s ease; display: flex; flex-direction: column; height: 100%;">
                        <div class="tour-card__image-wrapper" style="position: relative; height: 220px; overflow: hidden;">
                            <img src="{{ $tour->thumbnail ? asset('storage/' . $tour->thumbnail) : asset('images/hero_bg.jpg') }}" alt="{{ $tour->title }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                            @if($tour->duration)
                                <span class="tour-card__duration-badge" style="position: absolute; bottom: 20px; right: 20px; background: rgba(15,42,82,0.85); color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; backdrop-filter: blur(4px);">
                                    ⏳ {{ $tour->duration }}
                                </span>
                            @endif
                        </div>
                        <div class="tour-card__body" style="padding: 24px; display: flex; flex-direction: column; flex-grow: 1;">
                            <div class="tour-card__meta" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                @if($tour->location)
                                    <span class="tour-card__location" style="font-size: 0.85rem; color: var(--text-muted); display: inline-flex; align-items: center; gap: 4px;">
                                        📍 {{ $tour->location }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="tour-card__title" style="font-size: 1.25rem; font-weight: 700; margin-bottom: 12px; line-height: 1.4; color: var(--text-primary, #0F2A52);">
                                <a href="{{ route('tours.show', $tour->slug) }}" style="color: inherit; text-decoration: none; transition: color 0.3s;">
                                    {{ $tour->title }}
                                </a>
                            </h3>
                            <p class="tour-card__desc" style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px; flex-grow: 1;">
                                {{ Str::limit(strip_tags($tour->short_description), 100) }}
                            </p>
                            <div class="tour-card__footer" style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(0,0,0,0.06); padding-top: 16px; margin-top: auto;">
                                <div>
                                    <span style="font-size: 0.8rem; color: var(--text-muted); display: block;">{{ __('Mulai Dari') }}</span>
                                    <span style="font-size: 1.25rem; font-weight: 800; color: var(--orange, #F97316);">Rp {{ number_format($tour->price, 0, ',', '.') }}</span>
                                </div>
                                <a href="{{ route('tours.show', $tour->slug) }}" class="btn btn--primary" style="text-decoration: none; padding: 10px 20px; font-size: 0.9rem; font-weight: 600; border-radius: 8px;">
                                    {{ __('Detail Paket') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="pagination-wrapper" style="margin-top: 50px; display: flex; justify-content: center;">
                {{ $tours->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 80px 20px; background: var(--card-bg, #ffffff); border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
                <div style="font-size: 4rem; margin-bottom: 20px;">🗺️</div>
                <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 8px;">{{ __('Belum Ada Paket Wisata') }}</h3>
                <p style="color: var(--text-muted); max-width: 400px; margin: 0 auto 24px;">{{ __('Kami sedang menyiapkan paket perjalanan terbaik untuk kategori ini. Silakan hubungi kami untuk request kustom rute perjalanan!') }}</p>
                <a href="{{ route('home') }}#contact" class="btn btn--primary" style="text-decoration: none;">{{ __('Hubungi Kami') }}</a>
            </div>
        @endif
    </div>
</div>
@endsection
