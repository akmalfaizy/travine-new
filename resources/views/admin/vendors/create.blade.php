@extends('admin.layout')
@section('title', 'Tambah Vendor — Admin')

@push('styles')
<style>
.product-row {
    display: grid;
    grid-template-columns: 2fr 1fr 1.5fr 2fr 40px;
    gap: 8px;
    align-items: start;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.product-row:last-child { border-bottom: none; }
.product-head {
    display: grid;
    grid-template-columns: 2fr 1fr 1.5fr 2fr 40px;
    gap: 8px;
    padding: 6px 0 10px;
    border-bottom: 1px solid var(--glass-border);
    margin-bottom: 4px;
}
.product-head span {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--text-muted);
}
.btn-remove-row {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: rgba(239,68,68,0.12);
    color: #f87171;
    border: 1px solid rgba(239,68,68,0.25);
    cursor: pointer;
    font-size: 1rem;
    transition: background 0.2s;
}
.btn-remove-row:hover { background: rgba(239,68,68,0.25); }
@media(max-width:700px){
    .product-row,.product-head { grid-template-columns: 1fr; }
    .product-head { display: none; }
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

<form method="POST" action="{{ route('admin.vendors.store') }}" novalidate>
@csrf

{{-- === INFORMASI VENDOR === --}}
<div class="admin-card" style="margin-bottom:24px;">
    <h3 style="font-weight:700;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
        <span style="background:rgba(20,184,166,0.12);color:var(--teal);width:32px;height:32px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-size:1rem;">🏢</span>
        Informasi Vendor
    </h3>
    <div class="admin-grid admin-grid--2">
        <div class="form-group" style="grid-column:1/-1;">
            <label class="form-label">Nama Vendor <span style="color:#ef4444;">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                   placeholder="cth: CV. Maju Jaya Transport" required>
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
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}"
                   placeholder="cth: 0812-3456-7890" required>
        </div>
        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                   placeholder="email@vendor.com">
        </div>
        <div class="form-group">
            <label class="form-label">Nama PIC</label>
            <input type="text" name="pic_name" class="form-control" value="{{ old('pic_name') }}"
                   placeholder="Nama kontak person">
        </div>
        <div class="form-group" style="grid-column:1/-1;">
            <label class="form-label">Lokasi / Alamat</label>
            <input type="text" name="location" class="form-control" value="{{ old('location') }}"
                   placeholder="cth: Jl. Malioboro No. 10, Yogyakarta">
        </div>
        <div class="form-group" style="grid-column:1/-1;">
            <label class="form-label">Catatan</label>
            <textarea name="notes" class="form-control" rows="3"
                      placeholder="Catatan tambahan tentang vendor ini...">{{ old('notes') }}</textarea>
        </div>
        <div class="form-group" style="grid-column:1/-1;">
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', '1') ? 'checked' : '' }}
                       style="width:16px;height:16px;accent-color:var(--teal);">
                <span class="form-label" style="margin-bottom:0;">Vendor Aktif</span>
            </label>
        </div>
    </div>
</div>

{{-- === DAFTAR PRODUK / LAYANAN === --}}
<div class="admin-card" style="margin-bottom:24px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h3 style="font-weight:700;display:flex;align-items:center;gap:10px;">
            <span style="background:rgba(59,130,246,0.12);color:#60a5fa;width:32px;height:32px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-size:1rem;">📦</span>
            Daftar Produk / Layanan
            <span id="productCount" style="background:rgba(20,184,166,0.1);color:var(--teal);border-radius:20px;padding:2px 10px;font-size:0.75rem;font-weight:600;">0 item</span>
        </h3>
        <button type="button" onclick="addProductRow()" class="admin-btn admin-btn--secondary admin-btn--sm">
            + Tambah Produk
        </button>
    </div>

    <div class="product-head">
        <span>Nama Produk / Layanan</span>
        <span>Satuan</span>
        <span>Harga (Rp)</span>
        <span>Keterangan</span>
        <span></span>
    </div>

    <div id="productsContainer"></div>

    <div id="emptyProducts" style="text-align:center;padding:28px;color:var(--text-muted);font-size:0.88rem;">
        <div style="font-size:2rem;margin-bottom:8px;">📦</div>
        Belum ada produk. Klik <strong>+ Tambah Produk</strong> untuk menambahkan.
    </div>
</div>

{{-- Actions --}}
<div style="display:flex;justify-content:flex-end;gap:12px;">
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

function updateProductCounter() {
    const count = document.getElementById('productsContainer').children.length;
    document.getElementById('productCount').textContent = count + ' item';
    document.getElementById('emptyProducts').style.display = count === 0 ? '' : 'none';
}

function addProductRow(name='', unit='', price='', notes='') {
    const idx = productCount++;
    const row = document.createElement('div');
    row.className = 'product-row';
    row.id = 'prow-' + idx;
    row.innerHTML = `
        <input type="text" name="products[${idx}][name]" class="form-control" style="font-size:0.85rem;" placeholder="Nama produk/layanan" value="${escHtml(name)}">
        <input type="text" name="products[${idx}][unit]" class="form-control" style="font-size:0.85rem;" placeholder="pax/unit/hari" value="${escHtml(unit)}">
        <input type="number" name="products[${idx}][price]" class="form-control" style="font-size:0.85rem;" placeholder="0" value="${price}" min="0" step="any">
        <input type="text" name="products[${idx}][notes]" class="form-control" style="font-size:0.85rem;" placeholder="Keterangan opsional..." value="${escHtml(notes)}">
        <button type="button" onclick="removeRow('prow-${idx}')" class="btn-remove-row" title="Hapus baris">✕</button>
    `;
    document.getElementById('productsContainer').appendChild(row);
    updateProductCounter();
}

function removeRow(id) {
    document.getElementById(id)?.remove();
    updateProductCounter();
}

function escHtml(s) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(String(s || '')));
    return div.innerHTML;
}

// Pre-fill dari old() saat validasi error
const initialProducts = {!! json_encode(old('products', [])) !!};
if (Array.isArray(initialProducts) && initialProducts.length > 0) {
    initialProducts.forEach(p => {
        addProductRow(p.name || '', p.unit || '', p.price !== undefined && p.price !== null ? p.price : '', p.notes || '');
    });
} else {
    updateProductCounter();
}
</script>
@endpush
