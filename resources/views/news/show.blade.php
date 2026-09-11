@extends('layouts.app')

@section('content')
<style>
    .news-detail-page {
        padding: 120px 0 80px;
        background: var(--bg-main);
        color: var(--text-primary);
    }
    .news-detail-container {
        display: grid;
        grid-template-columns: 3fr 1fr;
        gap: 40px;
        margin-top: 30px;
    }
    @media (max-width: 991px) {
        .news-detail-container {
            grid-template-columns: 1fr;
        }
    }
    .article-card {
        background: var(--card-bg, #fff);
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        border: 1px solid rgba(0,0,0,0.03);
    }
    @media (max-width: 576px) {
        .article-card {
            padding: 24px 16px;
        }
    }
    .article-meta {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        font-size: 0.9rem;
        color: var(--text-muted);
        margin-bottom: 20px;
    }
    .article-meta span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .article-content {
        line-height: 1.8;
        font-size: 1.05rem;
    }
    .article-content p {
        margin-bottom: 24px;
    }
    .article-content h2, .article-content h3 {
        margin: 35px 0 15px;
        font-weight: 700;
        color: var(--text-primary);
    }
    .article-content h2 { font-size: 1.6rem; }
    .article-content h3 { font-size: 1.3rem; }
    .article-content blockquote {
        border-left: 4px solid var(--teal, #14B8A6);
        padding-left: 20px;
        margin: 30px 0;
        font-style: italic;
        color: var(--text-muted);
    }
    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 20px 0;
    }

    /* Sidebar Related Items */
    .sidebar-title {
        font-size: 1.2rem;
        font-weight: 750;
        margin-bottom: 20px;
        padding-bottom: 8px;
        border-bottom: 2px solid rgba(0,0,0,0.06);
    }
    .sidebar-item {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        text-decoration: none;
        color: inherit;
    }
    .sidebar-item:hover .sidebar-item-title {
        color: var(--teal, #14B8A6);
    }
    .sidebar-item-img {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        overflow: hidden;
        flex-shrink: 0;
    }
    .sidebar-item-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .sidebar-item-title {
        font-size: 0.95rem;
        font-weight: 600;
        line-height: 1.3;
        margin: 0 0 6px 0;
        transition: color 0.3s;
    }
    .sidebar-item-date {
        font-size: 0.8rem;
        color: var(--text-muted);
    }
</style>

<div class="news-detail-page">
    <div class="container">
        {{-- Breadcrumb / Back button --}}
        <div style="margin-bottom: 24px;">
            <a href="{{ route('news.index') }}" style="text-decoration: none; color: var(--teal, #14B8A6); font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                ← {{ __('Kembali ke Semua Artikel') }}
            </a>
        </div>

        <div class="news-detail-container">
            {{-- Article Content --}}
            <article class="article-card">
                <div class="article-meta">
                    <span>
                        ✍️ {{ $news->author }}
                    </span>
                    <span>
                        📅 {{ $news->published_at ? $news->published_at->translatedFormat('d F Y') : $news->created_at->translatedFormat('d F Y') }}
                    </span>
                </div>

                <h1 style="font-size: 2.5rem; font-weight: 850; line-height: 1.25; margin-bottom: 24px; color: var(--text-primary);">
                    {{ $news->title }}
                </h1>

                @if($news->thumbnail)
                    <div style="width:100%; max-height:480px; overflow:hidden; border-radius:12px; margin-bottom:35px; box-shadow:0 10px 30px rgba(0,0,0,0.03);">
                        <img src="{{ asset('storage/' . $news->thumbnail) }}" alt="{{ $news->title }}" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                @endif

                <div class="article-content">
                    {!! $news->content !!}
                </div>
            </article>

            {{-- Sidebar --}}
            <aside>
                @if($related->count() > 0)
                    <div class="article-card" style="padding: 24px;">
                        <h3 class="sidebar-title">{{ __('Artikel Terkait') }}</h3>
                        @foreach($related as $rel)
                            <a href="{{ route('news.show', $rel->slug) }}" class="sidebar-item">
                                <div class="sidebar-item-img">
                                    <img src="{{ $rel->thumbnail ? asset('storage/' . $rel->thumbnail) : asset('images/hero_bg.jpg') }}" alt="{{ $rel->title }}">
                                </div>
                                <div>
                                    <h4 class="sidebar-item-title">{{ Str::limit($rel->title, 45) }}</h4>
                                    <span class="sidebar-item-date">{{ $rel->published_at ? $rel->published_at->translatedFormat('d M Y') : $rel->created_at->translatedFormat('d M Y') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

                {{-- Contact / CTA widget --}}
                <div class="article-card" style="padding: 24px; margin-top: 30px; background: linear-gradient(135deg, #0F2A52 0%, #061124 100%); color: #fff; border:none; text-align: center;">
                    <div style="font-size: 2.5rem; margin-bottom: 12px;">🗺️</div>
                    <h3 style="color:#fff; font-size:1.15rem; font-weight:750; margin-bottom:8px;">{{ __('Butuh Bantuan Rencana Wisata?') }}</h3>
                    <p style="color:rgba(255,255,255,0.7); font-size:0.85rem; line-height:1.5; margin-bottom:20px;">
                        {{ __('Konsultasikan rute liburan impian Anda bersama Travine secara gratis!') }}
                    </p>
                    <a href="{{ route('home') }}#contact" class="btn btn--primary" style="text-decoration:none; display:block; padding:10px; font-size:0.9rem; font-weight:600; text-align:center;">
                        {{ __('Mulai Konsultasi') }}
                    </a>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
