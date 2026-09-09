@extends('admin.layout')
@section('title', isset($tour) ? 'Edit Paket Wisata' : 'Tambah Paket Wisata')
@section('content')
<div class="admin-topbar">
    <h1>{{ isset($tour) ? 'Edit Paket Wisata' : 'Tambah Paket Wisata' }}</h1>
    <a href="{{ route('admin.tours.index') }}" class="admin-btn admin-btn--secondary">← Kembali</a>
</div>

<form method="POST" action="{{ isset($tour) ? route('admin.tours.update', $tour) : route('admin.tours.store') }}" enctype="multipart/form-data">
    @csrf
    @if(isset($tour)) @method('PUT') @endif

    <div style="display:grid; grid-template-columns:1fr 360px; gap:20px; align-items:start;">
        {{-- Left Column --}}
        <div style="display:flex; flex-direction:column; gap:20px;">
            <div class="admin-card">
                <h3 style="font-weight:700; margin-bottom:20px; color:var(--text-primary);">Informasi Utama</h3>
                <div class="admin-grid admin-grid--2">
                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Judul Paket *</label>
                        <input name="title" type="text" class="form-control" value="{{ old('title', $tour->title ?? '') }}" placeholder="Paket Wisata Raja Ampat 4D3N" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori *</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $tour->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->icon }} {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga Publish / Jual (Rp) *</label>
                        <input name="price" id="admin_price" type="number" class="form-control" value="{{ old('price', $tour->price ?? '') }}" placeholder="1500000" required min="0" oninput="calcAdminCommission()">
                        <small style="color:var(--text-muted); font-size:0.78rem;">Harga resmi yang ditampilkan ke customer</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga Modal / HPP per Produk (Rp)</label>
                        <input name="hpp_price" id="admin_hpp" type="number" class="form-control" value="{{ old('hpp_price', $tour->hpp_price ?? '') }}" placeholder="1000000" min="0" oninput="calcAdminCommission()">
                        <small style="color:var(--text-muted); font-size:0.78rem;">Harga Pokok Penjualan / Biaya Vendor</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Komisi Mitra (%)</label>
                        <input name="commission_percentage" id="admin_comm_pct" type="number" step="0.1" class="form-control" value="{{ old('commission_percentage', $tour->commission_percentage ?? '') }}" placeholder="10" min="0" max="100" oninput="calcAdminCommission()">
                        <small style="color:var(--text-muted); font-size:0.78rem;">% komisi yang dihitung dari margin (Publish - HPP)</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Lokasi</label>
                        <input name="location" type="text" class="form-control" value="{{ old('location', $tour->location ?? '') }}" placeholder="Bali, Indonesia">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Durasi</label>
                        <input name="duration" type="text" class="form-control" value="{{ old('duration', $tour->duration ?? '') }}" placeholder="3 Hari 2 Malam">
                    </div>
                </div>

                {{-- Live Financial Preview --}}
                <div style="background:rgba(20,184,166,0.08); border:1px solid rgba(20,184,166,0.2); padding:16px; border-radius:10px; margin-top:16px;">
                    <div style="font-weight:700; font-size:0.9rem; color:var(--teal); margin-bottom:8px;">💡 Est. Margin & Komisi Mitra per Pax:</div>
                    <div style="display:flex; gap:24px; font-size:0.85rem; color:var(--text-primary);">
                        <div>Margin (Profit): <strong id="preview-margin">Rp 0</strong></div>
                        <div>Est. Komisi Mitra: <strong id="preview-comm" style="color:var(--orange,#F97316);">Rp 0</strong></div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi Singkat</label>
                    <textarea name="short_description" class="form-control" rows="3" placeholder="Ringkasan singkat paket wisata...">{{ old('short_description', $tour->short_description ?? '') }}</textarea>
                </div>
            </div>

            <div class="admin-card">
                <h3 style="font-weight:700; margin-bottom:20px;">Deskripsi Lengkap</h3>
                <div class="form-group">
                    <label class="form-label">Isi Deskripsi</label>
                    <textarea name="description" id="description" class="form-control" rows="8">{{ old('description', $tour->description ?? '') }}</textarea>
                </div>
            </div>

            <div class="admin-card">
                <h3 style="font-weight:700; margin-bottom:20px;">Detail Perjalanan</h3>
                <div class="form-group">
                    <label class="form-label">Itinerary (Jadwal Perjalanan)</label>
                    <textarea name="itinerary" class="form-control" rows="6" placeholder="Hari 1: Tiba di lokasi...&#10;Hari 2: ....">{{ old('itinerary', $tour->itinerary ?? '') }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Fasilitas</label>
                    <textarea name="facilities" class="form-control" rows="4" placeholder="✓ Akomodasi hotel&#10;✓ Makan 3x sehari">{{ old('facilities', $tour->facilities ?? '') }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Syarat &amp; Ketentuan</label>
                    <textarea name="terms" class="form-control" rows="4">{{ old('terms', $tour->terms ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div style="display:flex; flex-direction:column; gap:20px;">
            <div class="admin-card">
                <h3 style="font-weight:700; margin-bottom:20px;">Gambar Thumbnail</h3>
                @if(isset($tour) && $tour->thumbnail)
                    <img src="{{ Storage::url($tour->thumbnail) }}" style="width:100%; height:160px; object-fit:cover; border-radius:8px; margin-bottom:12px;">
                @endif
                <div class="form-group">
                    <input name="thumbnail" type="file" class="form-control" accept="image/*" onchange="previewImg(this,'thumb-preview')">
                    <img id="thumb-preview" style="width:100%; margin-top:10px; border-radius:8px; display:none;">
                </div>
            </div>

            <div class="admin-card">
                <h3 style="font-weight:700; margin-bottom:20px;">Galeri Foto</h3>
                @if(isset($tour) && $tour->gallery)
                    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:6px; margin-bottom:12px;">
                        @foreach($tour->gallery as $img)
                            <img src="{{ Storage::url($img) }}" style="width:100%; height:60px; object-fit:cover; border-radius:6px;">
                        @endforeach
                    </div>
                @endif
                <input name="gallery[]" type="file" class="form-control" accept="image/*" multiple>
                <small style="color:var(--text-muted); font-size:0.78rem;">Upload beberapa foto sekaligus. Foto baru akan ditambahkan ke galeri yang sudah ada.</small>
            </div>

            <div class="admin-card">
                <h3 style="font-weight:700; margin-bottom:16px;">Status</h3>
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; padding:12px; background:rgba(255,255,255,0.04); border-radius:8px;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $tour->is_active ?? true) ? 'checked' : '' }} style="accent-color:var(--teal); width:18px; height:18px;">
                    <div>
                        <div style="font-weight:600; color:var(--text-primary);">Aktifkan Paket</div>
                        <div style="font-size:0.8rem; color:var(--text-muted);">Tampilkan di halaman website</div>
                    </div>
                </label>
            </div>

            <button type="submit" class="admin-btn admin-btn--primary" style="width:100%; justify-content:center; padding:14px; font-size:1rem;">
                {{ isset($tour) ? 'Simpan Perubahan' : 'Tambah Paket Wisata' }}
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

function calcAdminCommission() {
    const price = parseFloat(document.getElementById('admin_price')?.value) || 0;
    const hpp = parseFloat(document.getElementById('admin_hpp')?.value) || 0;
    const commPct = parseFloat(document.getElementById('admin_comm_pct')?.value) || 0;

    const margin = Math.max(0, price - hpp);
    const commAmt = Math.round((margin * commPct) / 100);

    const marginElem = document.getElementById('preview-margin');
    const commElem = document.getElementById('preview-comm');

    if (marginElem) marginElem.innerText = 'Rp ' + margin.toLocaleString('id-ID');
    if (commElem) commElem.innerText = 'Rp ' + commAmt.toLocaleString('id-ID');
}
document.addEventListener('DOMContentLoaded', calcAdminCommission);
</script>
@endpush
