@extends('admin.layout')
@section('title', 'Paket Wisata')
@section('content')
<div class="admin-topbar">
    <h1>Paket Wisata</h1>
    <a href="{{ route('admin.tours.create') }}" class="admin-btn admin-btn--primary">+ Tambah Paket</a>
</div>
<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr><th>Thumbnail</th><th>Judul</th><th>Kategori</th><th>Harga Publish</th><th>HPP (Modal)</th><th>Komisi Mitra</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        @forelse($tours as $tour)
            <tr>
                <td>
                    @if($tour->thumbnail)
                        <img src="{{ Storage::url($tour->thumbnail) }}" style="width:60px; height:40px; object-fit:cover; border-radius:6px;">
                    @else
                        <div style="width:60px; height:40px; background:rgba(255,255,255,0.05); border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:1.2rem;">🏝️</div>
                    @endif
                </td>
                <td style="font-weight:600;">{{ Str::limit($tour->title, 40) }}</td>
                <td><span class="badge badge-info">{{ $tour->category?->name }}</span></td>
                <td style="font-weight:600; color:var(--teal);">{{ $tour->price_formatted }}</td>
                <td style="color:var(--text-muted); font-size:0.85rem;">{{ $tour->hpp_price_formatted }}</td>
                <td>
                    <span style="font-weight:700; color:var(--orange, #F97316);">{{ $tour->commission_percentage ?? 0 }}%</span>
                    <small style="display:block; font-size:0.75rem; color:var(--text-muted);">Est: {{ $tour->estimated_commission_formatted }}/pax</small>
                </td>
                <td><span class="badge {{ $tour->is_active ? 'badge-success' : 'badge-muted' }}">{{ $tour->is_active ? 'Aktif' : 'Draft' }}</span></td>
                <td>
                    <div style="display:flex; gap:6px;">
                        <a href="{{ route('admin.tours.edit', $tour) }}" class="admin-btn admin-btn--secondary admin-btn--sm">Edit</a>
                        <form method="POST" action="{{ route('admin.tours.destroy', $tour) }}" onsubmit="return confirm('Hapus paket wisata ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="admin-btn admin-btn--danger admin-btn--sm">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" style="text-align:center; color:var(--text-muted); padding:40px;">Belum ada paket wisata</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
@push('scripts')
<script>
// Ensure Laravel Storage URL is defined
</script>
@endpush
