@extends('admin.layout')
@section('title', 'Data Vendor — Admin')

@push('styles')
<style>
.vendor-badge { display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600; }
.vendor-badge.active   { background:rgba(34,197,94,0.12);color:#22c55e;border:1px solid rgba(34,197,94,0.25); }
.vendor-badge.inactive { background:rgba(255,255,255,0.06);color:var(--text-muted);border:1px solid var(--glass-border); }
.cat-badge { background:rgba(20,184,166,0.1);color:var(--teal);border:1px solid rgba(20,184,166,0.2);padding:3px 10px;border-radius:20px;font-size:0.75rem;font-weight:600; }
.filter-bar { display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin-bottom:24px; }
.filter-bar input,.filter-bar select { min-width:180px; }
</style>
@endpush

@section('content')
<div class="admin-topbar">
    <div>
        <h1>Data Vendor</h1>
        <p style="color:var(--text-muted);font-size:0.85rem;margin-top:4px;">Kelola daftar vendor & mitra travine.id</p>
    </div>
    <a href="{{ route('admin.vendors.create') }}" class="admin-btn admin-btn--primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Vendor
    </a>
</div>

{{-- Stats --}}
<div class="admin-grid admin-grid--4" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(20,184,166,0.1);">🏢</div>
        <div><div class="stat-value">{{ $totalVendors }}</div><div class="stat-label">Total Vendor</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(34,197,94,0.1);">✅</div>
        <div><div class="stat-value">{{ $activeVendors }}</div><div class="stat-label">Vendor Aktif</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,0.1);">📦</div>
        <div><div class="stat-value">{{ $totalProducts }}</div><div class="stat-label">Total Produk</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(251,191,36,0.1);">🗂️</div>
        <div><div class="stat-value">{{ $totalCategories }}</div><div class="stat-label">Kategori</div></div>
    </div>
</div>

{{-- Filter --}}
<form method="GET" action="{{ route('admin.vendors.index') }}" class="filter-bar">
    <input type="text" name="search" class="form-control" placeholder="🔍 Cari nama, telepon, lokasi..." value="{{ request('search') }}" style="max-width:280px;">
    <select name="category" class="form-control" style="max-width:200px;">
        <option value="">Semua Kategori</option>
        @foreach($categories as $cat)
            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
        @endforeach
    </select>
    <select name="status" class="form-control" style="max-width:150px;">
        <option value="">Semua Status</option>
        <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>Aktif</option>
        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
    </select>
    <button type="submit" class="admin-btn admin-btn--primary admin-btn--sm">Filter</button>
    @if(request()->hasAny(['search','category','status']))
        <a href="{{ route('admin.vendors.index') }}" class="admin-btn admin-btn--secondary admin-btn--sm">Reset</a>
    @endif
</form>

{{-- Table --}}
<div class="admin-card" style="padding:0;overflow:hidden;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Nama Vendor</th>
                <th>Kategori</th>
                <th>Telepon / PIC</th>
                <th>Lokasi</th>
                <th>Produk</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vendors as $vendor)
            <tr>
                <td>
                    <div style="font-weight:700;color:var(--text-primary);">{{ $vendor->name }}</div>
                    @if($vendor->email)
                        <div style="font-size:0.78rem;color:var(--text-muted);">{{ $vendor->email }}</div>
                    @endif
                </td>
                <td><span class="cat-badge">{{ $vendor->category }}</span></td>
                <td>
                    <div style="font-size:0.88rem;">{{ $vendor->phone }}</div>
                    @if($vendor->pic_name)
                        <div style="font-size:0.76rem;color:var(--text-muted);">PIC: {{ $vendor->pic_name }}</div>
                    @endif
                </td>
                <td style="font-size:0.88rem;color:var(--text-secondary);">{{ $vendor->location ?? '—' }}</td>
                <td>
                    <span class="badge badge-info">{{ $vendor->products_count }} produk</span>
                </td>
                <td>
                    <span class="vendor-badge {{ $vendor->is_active ? 'active' : 'inactive' }}">
                        {{ $vendor->is_active ? '● Aktif' : '○ Non-Aktif' }}
                    </span>
                </td>
                <td>
                    <div style="display:flex;gap:6px;align-items:center;">
                        <a href="{{ route('admin.vendors.show', $vendor) }}" class="admin-btn admin-btn--secondary admin-btn--sm">Detail</a>
                        <a href="{{ route('admin.vendors.edit', $vendor) }}" class="admin-btn admin-btn--warning admin-btn--sm">Edit</a>
                        <form method="POST" action="{{ route('admin.vendors.toggle', $vendor) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="admin-btn admin-btn--secondary admin-btn--sm"
                                    title="{{ $vendor->is_active ? 'Non-aktifkan' : 'Aktifkan' }}">
                                {{ $vendor->is_active ? '⏸' : '▶' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.vendors.destroy', $vendor) }}" style="display:inline;"
                              onsubmit="return confirm('Hapus vendor {{ addslashes($vendor->name) }} dan semua produknya?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="admin-btn admin-btn--danger admin-btn--sm">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">
                    <div style="font-size:2rem;margin-bottom:8px;">🏢</div>
                    Belum ada data vendor. <a href="{{ route('admin.vendors.create') }}" style="color:var(--teal);">Tambah sekarang →</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($vendors->hasPages())
<div class="pagination" style="margin-top:20px;">
    {{ $vendors->links() }}
</div>
@endif
@endsection
