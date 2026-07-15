@extends('admin.layout')
@section('title', isset($news) ? 'Edit Berita' : 'Tulis Berita')
@section('content')
<div class="admin-topbar">
    <h1>{{ isset($news) ? 'Edit Berita' : 'Tulis Berita Baru' }}</h1>
    <a href="{{ route('admin.news.index') }}" class="admin-btn admin-btn--secondary">← Kembali</a>
</div>

<form method="POST" action="{{ isset($news) ? route('admin.news.update', $news) : route('admin.news.store') }}" enctype="multipart/form-data">
    @csrf
    @if(isset($news)) @method('PUT') @endif

    <div style="display:grid; grid-template-columns:1fr 320px; gap:20px; align-items:start;">
        <div style="display:flex; flex-direction:column; gap:20px;">
            <div class="admin-card">
                <div class="form-group">
                    <label class="form-label">Judul Berita *</label>
                    <input name="title" type="text" class="form-control" value="{{ old('title', $news->title ?? '') }}" placeholder="Judul berita yang menarik..." required>
                </div>
                <div class="form-group">
                    <label class="form-label">Ringkasan</label>
                    <textarea name="summary" class="form-control" rows="3" placeholder="Ringkasan singkat berita (tampil di listing)...">{{ old('summary', $news->summary ?? '') }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Isi Berita *</label>
                    <textarea name="content" id="content" class="form-control" rows="16" required>{{ old('content', $news->content ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:20px;">
            <div class="admin-card">
                <h3 style="font-weight:700; margin-bottom:16px;">Pengaturan Publish</h3>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="draft" {{ old('status', $news->status ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $news->status ?? '') === 'published' ? 'selected' : '' }}>Publish</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Publish</label>
                    <input name="published_at" type="datetime-local" class="form-control"
                           value="{{ old('published_at', isset($news->published_at) ? $news->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Penulis</label>
                    <input name="author" type="text" class="form-control" value="{{ old('author', $news->author ?? auth()->user()->name) }}">
                </div>
            </div>

            <div class="admin-card">
                <h3 style="font-weight:700; margin-bottom:16px;">Thumbnail</h3>
                @if(isset($news) && $news->thumbnail)
                    <img src="{{ Storage::url($news->thumbnail) }}" style="width:100%; height:130px; object-fit:cover; border-radius:8px; margin-bottom:12px;">
                @endif
                <input name="thumbnail" type="file" class="form-control" accept="image/*" onchange="previewImg(this,'thumb-preview')">
                <img id="thumb-preview" style="width:100%; margin-top:10px; border-radius:8px; display:none;">
            </div>

            <button type="submit" class="admin-btn admin-btn--primary" style="width:100%; justify-content:center; padding:14px;">
                {{ isset($news) ? 'Simpan Perubahan' : 'Publish Berita' }}
            </button>
        </div>
    </div>
</form>
@endsection
@push('scripts')
<script>
function previewImg(input, id) {
    const preview = document.getElementById(id);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
