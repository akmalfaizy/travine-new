@extends('admin.layout')

@section('content')
<div style="margin-bottom: 32px;">
    <h1 style="font-size: 2rem; font-weight: 800;">Edit Project</h1>
    <p style="color: var(--text-muted);">Perbarui detail proyek wisata.</p>
</div>

<div class="admin-card" style="max-width: 800px;">
    <form action="{{ route('admin.portfolios.update', $portfolio) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="title" class="form-label">Project Title</label>
            <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $portfolio->title) }}" required>
        </div>

        <div class="form-group">
            <label for="category" class="form-label">Category</label>
            <select id="category" name="category" class="form-control" required>
                <option value="">Pilih Kategori</option>
                <option value="Jasa Wisata" {{ old('category', $portfolio->category) == 'Jasa Wisata' ? 'selected' : '' }}>Jasa Wisata</option>
                <option value="Pengembangan Ekosistem" {{ old('category', $portfolio->category) == 'Pengembangan Ekosistem' ? 'selected' : '' }}>Pengembangan Ekosistem</option>
                <option value="Konsultasi Destinasi" {{ old('category', $portfolio->category) == 'Konsultasi Destinasi' ? 'selected' : '' }}>Konsultasi Destinasi</option>
            </select>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Short Description</label>
            <textarea id="description" name="description" class="form-control" rows="3" required>{{ old('description', $portfolio->description) }}</textarea>
        </div>

        <div class="form-group">
            <label for="content" class="form-label">Full Details (Shown in Modal)</label>
            <textarea id="content" name="content" class="form-control" rows="6">{{ old('content', $portfolio->content) }}</textarea>
        </div>

        <div class="form-group">
            <label for="image_file" class="form-label">Project Image</label>
            @if($portfolio->image)
                <div style="margin-bottom: 12px;">
                    <img src="{{ asset('images/' . $portfolio->image) }}" alt="Current Image" style="width: 150px; border-radius: var(--radius-sm);">
                </div>
            @endif
            <input type="file" id="image_file" name="image_file" class="form-control" accept="image/*">
            <small style="color: var(--text-muted); display: block; margin-top: 8px;">Upload image (Max 2MB). Biarkan kosong jika tidak ingin mengubah gambar.</small>
        </div>

        <div style="display: flex; gap: 16px; margin-top: 32px;">
            <button type="submit" class="admin-btn admin-btn--primary">Update Project</button>
            <a href="{{ route('admin.portfolios.index') }}" class="admin-btn" style="background: rgba(255,255,255,0.05); color: var(--text-secondary);">Cancel</a>
        </div>
    </form>
</div>
@endsection
