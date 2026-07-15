@extends('admin.layout')
@section('title', 'Kategori Wisata')
@section('content')
<div class="admin-topbar">
    <h1>Kategori Wisata</h1>
    <a href="{{ route('admin.categories.create') }}" class="admin-btn admin-btn--primary">+ Tambah Kategori</a>
</div>
<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr><th>#</th><th>Ikon</th><th>Nama</th><th>Slug</th><th>Jumlah Wisata</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        @forelse($categories as $cat)
            <tr>
                <td style="color:var(--text-muted);">{{ $loop->iteration }}</td>
                <td style="font-size:1.5rem;">{{ $cat->icon }}</td>
                <td style="font-weight:600;">{{ $cat->name }}</td>
                <td style="color:var(--text-muted); font-size:0.85rem;">{{ $cat->slug }}</td>
                <td>{{ $cat->tours_count }} wisata</td>
                <td><span class="badge {{ $cat->is_active ? 'badge-success' : 'badge-muted' }}">{{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                <td>
                    <div style="display:flex; gap:6px;">
                        <a href="{{ route('admin.categories.edit', $cat) }}" class="admin-btn admin-btn--secondary admin-btn--sm">Edit</a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="admin-btn admin-btn--danger admin-btn--sm">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center; color:var(--text-muted); padding:40px;">Belum ada kategori</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
