@extends('admin.layout')
@section('title', $vendor->name . ' — Detail Vendor')

@push('styles')
<style>
.info-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 12px 0;
    border-bottom: 1px solid rgba(255,255,255,0.04);
}
.info-row:last-child { border-bottom: none; }
.info-label {
    font-size: 0.78rem;
    color: var(--text-muted);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    min-width: 120px;
    flex-shrink: 0;
}
.info-value {
    font-size: 0.9rem;
    color: var(--text-primary);
    font-weight: 500;
    text-align: right;
}
</style>
@endpush

@section('content')
<div class="admin-topbar">
    <div>
        <h1>{{ $vendor->name }}</h1>
        <p style="margin-top:6px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <span style="background:rgba(20,184,166,0.1);color:var(--teal);border:1px solid rgba(20,184,166,0.2);padding:3px 12px;border-radius:20px;font-size:0.75rem;font-weight:600;">{{ $vendor->category }}</span>
            <span style="background:{{ $vendor->is_active ? 'rgba(34,197,94,0.12)' : 'rgba(255,255,255,0.06)' }};color:{{ $vendor->is_active ? '#22c55e' : 'var(--text-muted)' }};border:1px solid {{ $vendor->is_active ? 'rgba(34,197,94,0.25)' : 'var(--glass-border)' }};padding:3px 12px;border-radius:20px;font-size:0.75rem;font-weight:600;">
                {{ $vendor->is_active ? '● Aktif' : '○ Non-Aktif' }}
            </span>
        </p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <form method="POST" action="{{ route('admin.vendors.toggle', $vendor) }}" style="display:inline;">
            @csrf
            <button type="submit" class="admin-btn admin-btn--secondary">
                {{ $vendor->is_active ? '⏸ Non-Aktifkan' : '▶ Aktifkan' }}
            </button>
        </form>
        <a href="{{ route('admin.vendors.edit', $vendor) }}" class="admin-btn admin-btn--warning">✏ Edit</a>
        <form method="POST" action="{{ route('admin.vendors.destroy', $vendor) }}"
              onsubmit="return confirm('Hapus vendor ini dan semua produknya?')">
            @csrf @method('DELETE')
            <button type="submit" class="admin-btn admin-btn--danger">🗑 Hapus</button>
        </form>
        <a href="{{ route('admin.vendors.index') }}" class="admin-btn admin-btn--secondary">← Kembali</a>
    </div>
</div>

@if(session('success'))
<div style="background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.35);border-radius:10px;padding:14px 20px;margin-bottom:20px;color:#86efac;font-weight:600;">
    ✅ {{ session('success') }}
</div>
@endif

{{-- === INFORMASI VENDOR === --}}
<div class="admin-card" style="margin-bottom:24px;">
    <h3 style="font-weight:700;margin-bottom:16px;color:var(--teal);display:flex;align-items:center;gap:10px;">
        <span style="background:rgba(20,184,166,0.12);width:32px;height:32px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-size:1rem;">🏢</span>
        Informasi Vendor
    </h3>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 40px;">
        <div>
            <div class="info-row">
                <span class="info-label">Kategori</span>
                <span class="info-value">{{ $vendor->category }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">No. Telepon</span>
                <span class="info-value">{{ $vendor->phone }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value">{{ $vendor->email ?? '—' }}</span>
            </div>
        </div>
        <div>
            <div class="info-row">
                <span class="info-label">Nama PIC</span>
                <span class="info-value">{{ $vendor->pic_name ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Lokasi</span>
                <span class="info-value">{{ $vendor->location ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Ditambahkan</span>
                <span class="info-value">{{ $vendor->created_at->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    @if($vendor->notes)
    <div style="margin-top:16px;padding:14px;background:rgba(255,255,255,0.03);border-radius:8px;font-size:0.87rem;color:var(--text-secondary);line-height:1.7;border:1px solid rgba(255,255,255,0.05);">
        <strong style="display:block;margin-bottom:6px;color:var(--text-muted);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.07em;">Catatan</strong>
        {{ $vendor->notes }}
    </div>
    @endif
</div>

{{-- === DAFTAR PRODUK & LAYANAN === --}}
<div class="admin-card" style="padding:0;overflow:hidden;">
    <div style="padding:20px 24px;border-bottom:1px solid var(--glass-border);display:flex;justify-content:space-between;align-items:center;">
        <h3 style="font-weight:700;display:flex;align-items:center;gap:10px;">
            <span style="background:rgba(59,130,246,0.12);color:#60a5fa;width:32px;height:32px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-size:1rem;">📦</span>
            Produk &amp; Layanan
            <span style="background:rgba(20,184,166,0.1);color:var(--teal);border-radius:20px;padding:2px 10px;font-size:0.75rem;font-weight:600;">{{ $vendor->products->count() }} item</span>
        </h3>
        <a href="{{ route('admin.vendors.edit', $vendor) }}" class="admin-btn admin-btn--secondary admin-btn--sm">✏ Kelola Produk</a>
    </div>

    @if($vendor->products->count())
    <table class="admin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Produk / Layanan</th>
                <th>Satuan</th>
                <th style="text-align:right;">Harga</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vendor->products as $i => $product)
            <tr>
                <td style="color:var(--text-muted);font-size:0.8rem;">{{ $i + 1 }}</td>
                <td style="font-weight:600;">{{ $product->name }}</td>
                <td style="font-size:0.85rem;color:var(--text-muted);">{{ $product->unit ?? '—' }}</td>
                <td style="text-align:right;font-weight:700;font-family:monospace;color:var(--teal);">
                    {{ $product->price > 0 ? $product->price_formatted : '—' }}
                </td>
                <td style="font-size:0.85rem;color:var(--text-secondary);">{{ $product->notes ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align:center;padding:48px;color:var(--text-muted);">
        <div style="font-size:2.5rem;margin-bottom:12px;">📦</div>
        <div style="font-weight:600;margin-bottom:8px;">Belum ada produk terdaftar</div>
        <a href="{{ route('admin.vendors.edit', $vendor) }}" style="color:var(--teal);font-size:0.88rem;">+ Tambah produk sekarang →</a>
    </div>
    @endif
</div>
@endsection
