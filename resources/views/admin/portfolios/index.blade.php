@extends('admin.layout')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;">
    <div>
        <h1 style="font-size: 2rem; font-weight: 800;">Manage Portfolio</h1>
        <p style="color: var(--text-muted);">Daftar semua proyek wisata yang ditampilkan di website.</p>
    </div>
    <a href="{{ route('admin.portfolios.create') }}" class="admin-btn admin-btn--primary">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Proyek
    </a>
</div>

<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Project Details</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($portfolios as $portfolio)
            <tr>
                <td style="width: 100px;">
                    <img src="{{ asset('images/' . ($portfolio->image ?? 'placeholder.webp')) }}" alt="" style="width: 80px; height: 60px; object-fit: cover; border-radius: var(--radius-sm);">
                </td>
                <td>
                    <div style="font-weight: 700; font-size: 1rem;">{{ $portfolio->title }}</div>
                    <div style="font-size: 0.85rem; color: var(--text-muted); max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {{ $portfolio->description }}
                    </div>
                </td>
                <td>
                    <span style="padding: 4px 10px; border-radius: 50px; background: rgba(20, 184, 166, 0.1); color: var(--teal); font-size: 0.75rem; font-weight: 600;">
                        {{ $portfolio->category }}
                    </span>
                </td>
                <td>
                    <div style="display: flex; gap: 8px;">
                        <a href="{{ route('admin.portfolios.edit', $portfolio) }}" class="admin-btn" style="background: rgba(255,255,255,0.05); color: var(--text-secondary);">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                        <form action="{{ route('admin.portfolios.destroy', $portfolio) }}" method="POST" onsubmit="return confirm('Hapus proyek ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="admin-btn admin-btn--danger">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
