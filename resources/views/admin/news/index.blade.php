@extends('admin.layout')
@section('title', 'Manajemen Berita')
@section('content')
<div class="admin-topbar">
    <h1>Manajemen Berita</h1>
    <a href="{{ route('admin.news.create') }}" class="admin-btn admin-btn--primary">+ Tulis Berita</a>
</div>
<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr><th>Thumbnail</th><th>Judul</th><th>Penulis</th><th>Tgl Publish</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        @forelse($news as $item)
            <tr>
                <td>
                    @if($item->thumbnail)
                        <img src="{{ Storage::url($item->thumbnail) }}" style="width:70px; height:45px; object-fit:cover; border-radius:6px;">
                    @else
                        <div style="width:70px; height:45px; background:rgba(255,255,255,0.05); border-radius:6px; display:flex; align-items:center; justify-content:center;">📰</div>
                    @endif
                </td>
                <td style="font-weight:600; max-width:280px;">{{ Str::limit($item->title, 55) }}</td>
                <td style="color:var(--text-muted);">{{ $item->author }}</td>
                <td style="color:var(--text-muted); font-size:0.85rem;">{{ $item->published_at ? $item->published_at->format('d M Y') : '-' }}</td>
                <td>
                    <span class="badge {{ $item->status === 'published' ? 'badge-success' : 'badge-muted' }}">
                        {{ $item->status === 'published' ? 'Publish' : 'Draft' }}
                    </span>
                </td>
                <td>
                    <div style="display:flex; gap:6px;">
                        <a href="{{ route('admin.news.edit', $item) }}" class="admin-btn admin-btn--secondary admin-btn--sm">Edit</a>
                        <form method="POST" action="{{ route('admin.news.destroy', $item) }}" onsubmit="return confirm('Hapus berita ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="admin-btn admin-btn--danger admin-btn--sm">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding:40px;">Belum ada berita</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
