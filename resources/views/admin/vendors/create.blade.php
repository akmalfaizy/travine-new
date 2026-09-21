@extends('admin.layout')
@section('title', 'Tambah Vendor — Admin')

@push('styles')
<style>
.product-row { display:grid;grid-template-columns:2fr 1fr 1.5fr 2fr 36px;gap:8px;align-items:start;padding:10px 0;border-bottom:1px solid rgba(255,255,255,0.04); }
.product-row:last-child { border-bottom:none; }
.product-labels { display:grid;grid-template-columns:2fr 1fr 1.5fr 2fr 36px;gap:8px;padding:4px 0 8px;border-bottom:1px solid var(--glass-border);margin-bottom:8px; }
.product-labels span { font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-muted); }
@media(max-width:700px){
    .product-row,.product-labels { grid-template-columns:1fr; }
    .product-labels { display:none; }
}
</style>
@endpush

@section('content')
<div class="admin-topbar">
    <div>
        <h1>Tambah Vendor Baru</h1>
        <p style="color:var(--text-muted);font-size:0.85rem;margin-top:4px;">Isi data vendor beserta daftar produk / layanannya</p>
    </div>
    <a href="{{ route('admin.vendors.index') }}" class="admin-btn admin-btn--secondary">← Kembali</a>
</div>

@if($errors->any())
<div style="background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.4);border-radius:10px;padding:16px 20px;margin-bottom:20px;">
    <div style="font-weight:700;color:#f87171;margin-bottom:8px;">⚠ Periksa kembali:</div>
    <ul style="margin:0;padding-left:20px;color:#fca5a5;font-size:0.88rem;">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('admin.vendors.store') }}">
@csrf

<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;align-items:start;">

    {{-- Left: Vendor Info --}}
    <div class="admin-card">
        <h3 style="font-weight:700;margin-bottom:20px;">Informasi Vendor</h3>
        <div class="admin-grid admin-grid--2">
            <div class="form-group" style="grid-column:1/-1;">
                <label class="form-label">Nama Vendor <span style="color:#ef4444;">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="cth: CV. Maju Jaya Transport" required>
            </div>
            <div class="form-group">
                <label class="form-label">Kategori <span style="color:#ef4444;">*</span></label>
                <select name="category" class="form-control" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">No. Telepon <span style="color:#ef4444;">*</span></label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="cth: 0812-3456-7890" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="email@vendor.com">
            </div>
            <div class="form-group">
                <label class="form-label">Nama PIC</label>
                <input type="text" name="pic_name" class="form-control" value="{{ old('pic_name') }}" placeholder="Nama kontak person">
            </div>
            <div class="form-group" style="grid-column:1/-1;">
                <label class="form-label">Lokasi / Alamat</label>
                <input type="text" name="location" class="form-control" value="{{ old('location') }}" placeholder="cth: Jl. Malioboro No. 10, Yogyakarta">
            </div>
            <div class="form-group" style="grid-column:1/-1;">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Catatan tambahan tentang vendor ini...">{{ old('notes') }}</textarea>
            </div>
            <div class="form-group" style="grid-column:1/-1;">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                           style="width:16px;height:16px;accent-color:var(--teal);">
                    <span class="form-label" style="margin-bottom:0;">Vendor Aktif</span>
                </label>
            </div>
        </div>
    </div>

    {{-- Right: Products --}}
    <div class="admin-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-weight:700;">Daftar Produk / Layanan</h3>
            <button type="button" onclick="addProductRow()" class="admin-btn admin-btn--secondary admin-btn--sm">+ Tambah</button>
        </div>

        <div class="product-labels">
            <span>Nama Produk</span><span>Satuan</span><span>Harga (Rp)</span><span>Keterangan</span><span></span>
        </div>

        <div id="productsContainer">
            {{-- JS will render rows --}}
        </div>
        <div id="emptyProducts" style="text-align:center;padding:20px;color:var(--text-muted);font-size:0.88rem;">
            Belum ada produk. Klik "+ Tambah" untuk menambahkan.
        </div>
    </div>
</div>

<div style="display:flex;justify-content:flex-end;gap:12px;margin-top:20px;">
    <a href="{{ route('admin.vendors.index') }}" class="admin-btn admin-btn--secondary">Batal</a>
    <button type="submit" class="admin-btn admin-btn--primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Simpan Vendor
    </button>
</div>

</form>
@endsection

@push('scripts')
<script>
let productCount = 0;

function addProductRow(name='', unit='', price='', notes='') {
    const idx = productCount++;
    document.getElementById('emptyProducts').style.display = 'none';
    const row = document.createElement('div');
    row.className = 'product-row';
    row.id = 'prow-' + idx;
    row.innerHTML = `
        <input type="text" name="products[${idx}][name]" class="form-control" style="font-size:0.85rem;" placeholder="Nama produk/layanan" value="${escHtml(name)}" required>
        <input type="text" name="products[${idx}][unit]" class="form-control" style="font-size:0.85rem;" placeholder="pax/unit/hari" value="${escHtml(unit)}">
        <input type="number" name="products[${idx}][price]" class="form-control" style="font-size:0.85rem;" placeholder="0" value="${price}" min="0" step="any">
        <input type="text" name="products[${idx}][notes]" class="form-control" style="font-size:0.85rem;" placeholder="Keterangan..." value="${escHtml(notes)}">
        <button type="button" onclick="removeRow('prow-${idx}')" class="admin-btn admin-btn--danger admin-btn--sm" style="padding:8px 10px;">✕</button>
    `;
    document.getElementById('productsContainer').appendChild(row);
}

function removeRow(id) {
    document.getElementById(id)?.remove();
    if (document.getElementById('productsContainer').children.length === 0) {
        document.getElementById('emptyProducts').style.display = '';
    }
}

function escHtml(s) {
    return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Pre-fill from old() on validation error
@php
    $oldProducts = old('products', []);
@endphp
@if(!empty($oldProducts))
@foreach($oldProducts as $p)
addProductRow(
    {{ json_encode($p['name'] ?? '') }},
    {{ json_encode($p['unit'] ?? '') }},
    {{ json_encode($p['price'] ?? '') }},
    {{ json_encode($p['notes'] ?? '') }}
);
@endforeach
@endif
</script>
@endpush
