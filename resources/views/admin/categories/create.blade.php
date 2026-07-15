@extends('admin.layout')
@section('title', 'Tambah Kategori')
@section('content')
<div class="admin-topbar">
    <h1>Tambah Kategori</h1>
    <a href="{{ route('admin.categories.index') }}" class="admin-btn admin-btn--secondary">← Kembali</a>
</div>
<div class="admin-card" style="max-width:600px;">
    <form method="POST" action="{{ route('admin.categories.store') }}">
        @csrf
        <div class="form-group">
            <label class="form-label">Nama Kategori *</label>
            <input name="name" type="text" class="form-control" value="{{ old('name') }}" placeholder="mis. Wisata Alam" required>
        </div>
        <div class="form-group">
            <label class="form-label">Ikon (Emoji)</label>
            <input name="icon" type="text" class="form-control" value="{{ old('icon') }}" placeholder="🌿" style="font-size:1.4rem;">
            <small style="color:var(--text-muted); font-size:0.8rem;">Tempel emoji dari keyboard atau copy-paste</small>
        </div>
        <div class="form-group">
            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} style="accent-color:var(--teal);">
                <span class="form-label" style="margin:0;">Aktif (tampil di website)</span>
            </label>
        </div>
        <button type="submit" class="admin-btn admin-btn--primary">Simpan Kategori</button>
    </form>
</div>
@endsection
