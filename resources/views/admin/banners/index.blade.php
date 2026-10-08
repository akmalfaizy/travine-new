@extends('admin.layout')

@section('title', 'Kelola Banner Slide Promo')

@section('content')
<div class="admin-topbar">
    <div>
        <h1>🎞️ Kelola Banner Slide Promo</h1>
        <p style="color:var(--text-muted); font-size:0.85rem; margin-top:4px;">
            Banner slide interaktif yang tampil di halaman depan untuk mengiklankan Open Trip & Promo wisata.
        </p>
    </div>
    <a href="{{ route('admin.banners.create') }}" class="admin-btn admin-btn--primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span>Tambah Banner Baru</span>
    </a>
</div>

@if(session('success'))
    <div class="admin-alert admin-alert--success">✓ {{ session('success') }}</div>
@endif

<div class="admin-card" style="padding:0; overflow:hidden;">
    @if($banners->count() > 0)
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width:70px; text-align:center;">Urutan</th>
                    <th style="width:160px;">Banner</th>
                    <th>Judul & Badge</th>
                    <th>Tujuan Klik (Open Trip / Link)</th>
                    <th style="text-align:center;">Pop-Up</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($banners as $banner)
                    <tr>
                        <td style="text-align:center;">
                            <span class="badge badge-muted" style="font-weight:700; font-size:0.85rem;">{{ $banner->order }}</span>
                        </td>
                        <td>
                            <div style="width:140px; aspect-ratio:16/9; border-radius:8px; overflow:hidden; border:1px solid var(--glass-border); background:#000; position:relative;">
                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" style="width:100%; height:100%; object-fit:cover;">
                                @if($banner->mobile_image)
                                    <span title="Tersedia banner versi mobile 1080x1080 (1:1)" style="position:absolute; bottom:4px; right:4px; font-size:0.65rem; background:rgba(0,0,0,0.75); padding:1px 5px; border-radius:4px; color:#fff; border:1px solid rgba(255,255,255,0.2);">📱 1:1</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($banner->tag)
                                <span style="display:inline-block; font-size:0.72rem; font-weight:700; color:var(--teal); background:rgba(20,184,166,0.12); padding:2px 8px; border-radius:4px; margin-bottom:4px;">
                                    {{ $banner->tag }}
                                </span>
                            @endif
                            <div style="font-weight:700; color:var(--text-primary); font-size:0.95rem;">
                                {{ $banner->title }}
                            </div>
                            @if($banner->subtitle)
                                <small style="color:var(--text-muted); display:block; max-width:300px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $banner->subtitle }}
                                </small>
                            @endif
                        </td>
                        <td>
                            @if($banner->tour)
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <span style="font-size:1rem;">🎒</span>
                                    <div>
                                        <strong style="color:var(--teal); font-size:0.85rem; display:block;">{{ $banner->tour->title }}</strong>
                                        <a href="{{ route('tours.show', $banner->tour->slug) }}" target="_blank" style="color:var(--text-muted); font-size:0.75rem; text-decoration:underline;">Lihat Halaman Tour ↗</a>
                                    </div>
                                </div>
                            @elseif($banner->link_url)
                                <a href="{{ $banner->link_url }}" target="_blank" style="color:var(--teal); font-size:0.85rem; word-break:break-all;">
                                    🔗 {{ Str::limit($banner->link_url, 40) }} ↗
                                </a>
                            @else
                                <span style="color:var(--text-muted); font-size:0.8rem;">(Tanpa Tautan / Hanya Info)</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            @if($banner->is_popup)
                                <span class="badge badge-warning" title="Ditampilkan juga sebagai pop-up saat pengunjung pertama buka web">Pop-up Aktif</span>
                            @else
                                <span style="color:var(--text-muted); font-size:0.8rem;">-</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <form action="{{ route('admin.banners.toggle', $banner) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" style="background:none; border:none; cursor:pointer;" title="Klik untuk ubah status">
                                    @if($banner->is_active)
                                        <span class="badge badge-success">✓ Aktif</span>
                                    @else
                                        <span class="badge badge-danger">Nonaktif</span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            <a href="{{ route('admin.banners.edit', $banner) }}" class="admin-btn admin-btn--secondary admin-btn--sm">
                                ✏️ Edit
                            </a>
                            <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus banner ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="admin-btn admin-btn--danger admin-btn--sm">
                                    🗑️
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="padding:16px 24px;">
            {{ $banners->links() }}
        </div>
    @else
        <div style="text-align:center; padding:60px 20px; color:var(--text-muted);">
            <div style="font-size:3rem; margin-bottom:12px;">🎞️</div>
            <p style="font-size:1rem; font-weight:700; color:var(--text-primary); margin-bottom:6px;">Belum Ada Banner Slide</p>
            <p style="font-size:0.85rem; max-width:400px; margin:0 auto 20px;">
                Tambahkan banner slide promo pertama Anda untuk mempromosikan Open Trip spesial atau penawaran menarik kepada pengunjung website.
            </p>
            <a href="{{ route('admin.banners.create') }}" class="admin-btn admin-btn--primary">
                + Tambah Banner Pertama
            </a>
        </div>
    @endif
</div>
@endsection
