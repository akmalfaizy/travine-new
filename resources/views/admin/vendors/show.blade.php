@extends('admin.layout')
@section('title', '{{ $vendor->name }} — Vendor Detail')

@section('content')
<div class="admin-topbar">
    <div>
        <h1>{{ $vendor->name }}</h1>
        <p style="color:var(--text-muted);font-size:0.85rem;margin-top:4px;">
            <span class="cat-badge" style="background:rgba(20,184,166,0.1);color:var(--teal);border:1px solid rgba(20,184,166,0.2);padding:3px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;">{{ $vendor->category }}</span>
            &nbsp;
            <span style="background:{{ $vendor->is_active ? 'rgba(34,197,94,0.12)' : 'rgba(255,255,255,0.06)' }};color:{{ $vendor->is_active ? '#22c55e' : 'var(--text-muted)' }};border:1px solid {{ $vendor->is_active ? 'rgba(34,197,94,0.25)' : 'var(--glass-border)' }};padding:3px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;">
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

<div style="display:grid;grid-template-columns:1fr 2fr;gap:24px;align-items:start;">

    {{-- Vendor Info Card --}}
    <div class="admin-card">
        <h3 style="font-weight:700;margin-bottom:20px;color:var(--teal);">Informasi Vendor</h3>

        @php
        $info = [
            ['label' => 'Kategori',    'value' => $vendor->category],
            ['label' => 'No. Telepon', 'value' => $vendor->phone],
            ['label' => 'Email',       'value' => $vendor->email ?? '—'],
            ['label' => 'Nama PIC',    'value' => $vendor->pic_name ?? '—'],
            ['label' => 'Lokasi',      'value' => $vendor->location ?? '—'],
            ['label' => 'Ditambahkan', 'value' => $vendor->created_at->format('d M Y')],
        ];
        @endphp

        @foreach($info as $row)
        <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:10px 0;border-bottom:1px solid rgba(255,255,255,0.04);">
            <span style="font-size:0.8rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.04em;min-width:100px;">{{ $row['label'] }}</span>
            <span style="font-size:0.9rem;color:var(--text-primary);text-align:right;font-weight:500;">{{ $row['value'] }}</span>
        </div>
        @endforeach

        @if($vendor->notes)
        <div style="margin-top:16px;padding:12px;background:rgba(255,255,255,0.03);border-radius:8px;font-size:0.87rem;color:var(--text-secondary);line-height:1.6;">
            <strong style="display:block;margin-bottom:4px;color:var(--text-muted);font-size:0.75rem;text-transform:uppercase;">Catatan</strong>
            {{ $vendor->notes }}
        </div>
        @endif
    </div>

    {{-- Products Table --}}
    <div class="admin-card" style="padding:0;overflow:hidden;">
        <div style="padding:20px 24px;border-bottom:1px solid var(--glass-border);display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-weight:700;">Produk & Layanan
                <span style="background:rgba(20,184,166,0.1);color:var(--teal);border-radius:20px;padding:2px 10px;font-size:0.75rem;margin-left:8px;">{{ $vendor->products->count() }}</span>
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
        <div style="text-align:center;padding:40px;color:var(--text-muted);">
            <div style="font-size:2rem;margin-bottom:8px;">📦</div>
            Belum ada produk terdaftar.
            <a href="{{ route('admin.vendors.edit', $vendor) }}" style="color:var(--teal);display:block;margin-top:8px;">+ Tambah produk →</a>
        </div>
        @endif
    </div>

</div>
@endsection
