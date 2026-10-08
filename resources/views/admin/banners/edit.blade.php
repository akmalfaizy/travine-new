@extends('admin.layout')

@section('title', 'Edit Banner Slide Promo')

@section('content')
<div class="admin-topbar">
    <div>
        <h1>✏️ Edit Banner Slide Promo</h1>
        <p style="color:var(--text-muted); font-size:0.85rem; margin-top:4px;">
            Perbarui data gambar, link tujuan open trip, atau status banner #{{ $banner->id }}.
        </p>
    </div>
    <a href="{{ route('admin.banners.index') }}" class="admin-btn admin-btn--secondary">
        ← Kembali
    </a>
</div>

<div class="admin-card" style="max-width:850px; margin:0 auto;">
    <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Row 1: Judul & Badge --}}
        <div class="admin-grid admin-grid--2">
            <div class="form-group">
                <label class="form-label">Judul Banner *</label>
                <input type="text" name="title" class="form-control" placeholder="Contoh: Open Trip Bromo Midnight Sunrise" value="{{ old('title', $banner->title) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Label / Tag Badge Promo (Opsional)</label>
                <input type="text" name="tag" class="form-control" placeholder="Contoh: 🔥 Open Trip Populer / Diskon 20%" value="{{ old('tag', $banner->tag) }}">
            </div>
        </div>

        {{-- Subtitle / Deskripsi Singkat --}}
        <div class="form-group">
            <label class="form-label">Deskripsi Singkat / Highlight Promo (Opsional)</label>
            <input type="text" name="subtitle" class="form-control" placeholder="Contoh: Keberangkatan setiap weekend, fasilitas lengkap bonus dokumentasi drone!" value="{{ old('subtitle', $banner->subtitle) }}">
        </div>

        {{-- Row 2: Target Link (Pilih Tour atau Custom Link) --}}
        <div style="background:rgba(20,184,166,0.06); border:1px dashed rgba(20,184,166,0.3); border-radius:10px; padding:20px; margin-bottom:20px;">
            <h4 style="font-size:0.95rem; font-weight:700; color:var(--teal); margin-bottom:12px; display:flex; align-items:center; gap:8px;">
                🎯 Target Tautan Ketika Banner Diklik
            </h4>

            <div class="form-group">
                <label class="form-label">Pilih Paket Wisata / Open Trip Tujuan (Otomatis):</label>
                <select name="tour_id" class="form-control" style="font-weight:600;">
                    <option value="">-- Pilih Paket Tour yang Diiklankan --</option>
                    @foreach($tours as $t)
                        <option value="{{ $t->id }}" {{ old('tour_id', $banner->tour_id) == $t->id ? 'selected' : '' }}>
                            {{ $t->title }} ({{ $t->price_formatted }})
                        </option>
                    @endforeach
                </select>
                <small style="color:var(--text-muted); font-size:0.78rem; display:block; margin-top:4px;">
                    Jika dipilih, saat pengunjung mengklik banner mereka akan langsung diarahkan ke halaman detail open trip tersebut.
                </small>
            </div>

            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Atau URL Khusus (Jika bukan paket tour di atas):</label>
                <input type="url" name="link_url" class="form-control" placeholder="https://..." value="{{ old('link_url', $banner->link_url) }}">
            </div>
        </div>

        {{-- Row 3: Gambar Banner Saat Ini & Ganti Baru --}}
        <div class="admin-grid admin-grid--2">
            <div class="form-group">
                <label class="form-label">Gambar Banner Utama (Desktop / Universal)</label>
                @if($banner->image)
                    <div style="margin-bottom:8px; width:100%; max-height:120px; overflow:hidden; border-radius:6px; border:1px solid var(--glass-border);">
                        <img src="{{ $banner->image_url }}" alt="Preview" style="width:100%; height:120px; object-fit:cover;">
                    </div>
                @endif
                <input type="file" name="image" class="form-control" accept="image/*">
                <small style="color:var(--text-muted); font-size:0.78rem; display:block; margin-top:4px;">
                    Rekomendasi rasio: <strong>16:9</strong> (Ukuran ideal: 1920x1080px atau 1280x720px). Tampil pada layar desktop / laptop. Biarkan kosong jika tidak ingin mengganti.
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">Gambar Khusus Layar HP / Mobile (Opsional)</label>
                @if($banner->mobile_image)
                    <div style="margin-bottom:8px; width:120px; height:120px; aspect-ratio:1/1; overflow:hidden; border-radius:6px; border:1px solid var(--glass-border);">
                        <img src="{{ $banner->mobile_image_url }}" alt="Preview Mobile" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                @endif
                <input type="file" name="mobile_image" class="form-control" accept="image/*">
                <small style="color:var(--text-muted); font-size:0.78rem; display:block; margin-top:4px;">
                    Rekomendasi rasio: <strong>1:1 / Feed Instagram</strong> (Ukuran ideal: 1080x1080px). Khusus tampil pada layar HP / smartphone. Biarkan kosong jika tidak ada gambar mobile baru.
                </small>
            </div>
        </div>

        {{-- Row 4: Tombol & Urutan --}}
        <div class="admin-grid admin-grid--2">
            <div class="form-group">
                <label class="form-label">Teks Tombol CTA</label>
                <input type="text" name="button_text" class="form-control" placeholder="Lihat Paket" value="{{ old('button_text', $banner->button_text) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Urutan Tampil (Order)</label>
                <input type="number" name="order" class="form-control" min="0" value="{{ old('order', $banner->order) }}">
                <small style="color:var(--text-muted); font-size:0.78rem; display:block; margin-top:4px;">Angka lebih kecil tampil lebih awal (0, 1, 2, ...)</small>
            </div>
        </div>

        {{-- Row 5: Pengaturan Aktif & Pop-up --}}
        <div style="background:var(--bg-main); border:1px solid var(--glass-border); border-radius:10px; padding:16px 20px; margin-bottom:24px; display:flex; flex-direction:column; gap:12px;">
            <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }} style="width:18px; height:18px; accent-color:var(--teal);">
                <span style="font-weight:600; color:var(--text-primary);">Aktifkan Banner di Slider Halaman Depan</span>
            </label>

            <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                <input type="checkbox" name="is_popup" value="1" {{ old('is_popup', $banner->is_popup) ? 'checked' : '' }} style="width:18px; height:18px; accent-color:#f59e0b;">
                <span style="font-weight:600; color:#fbbf24;">⚡ Jadikan juga Pop-Up Sambutan (Muncul saat pengunjung pertama kali membuka website)</span>
            </label>
        </div>

        <div style="display:flex; gap:12px; justify-content:flex-end;">
            <a href="{{ route('admin.banners.index') }}" class="admin-btn admin-btn--secondary">Batal</a>
            <button type="submit" class="admin-btn admin-btn--primary" style="padding:10px 24px;">
                💾 Perbarui Banner
            </button>
        </div>
    </form>
</div>
@endsection
