@extends('admin.layout')
@section('title', 'Edit Kategori')
@section('content')
<div class="admin-topbar">
    <h1>Edit Kategori</h1>
    <a href="{{ route('admin.categories.index') }}" class="admin-btn admin-btn--secondary">← Kembali</a>
</div>
<div class="admin-card" style="max-width:600px;">
    <form method="POST" action="{{ route('admin.categories.update', $category) }}">
        @csrf @method('PUT')
        <div class="form-group">
            <label class="form-label">Nama Kategori *</label>
            <input name="name" type="text" class="form-control" value="{{ old('name', $category->name) }}" required>
        </div>
        <div class="form-group">
            <label class="form-label">Ikon (Emoji)</label>
            <input name="icon" type="text" class="form-control" value="{{ old('icon', $category->icon) }}" style="font-size:1.4rem;">
        </div>
        <div class="form-group">
            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} style="accent-color:var(--teal);">
                <span class="form-label" style="margin:0;">Aktif</span>
            </label>
        </div>
        <button type="submit" class="admin-btn admin-btn--primary">Simpan Perubahan</button>
    </form>
</div>
@endsection
