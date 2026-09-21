@extends('admin.layout')
@section('title', 'Daftar Offering — Admin')
@section('content')

<div class="admin-topbar">
    <div>
        <h1>Offering / Production Cost</h1>
        <p style="color:var(--text-muted);font-size:0.85rem;margin-top:4px;">Kelola dokumen penawaran harga project kepada klien</p>
    </div>
    <a href="{{ route('admin.offerings.create') }}" class="admin-btn admin-btn--primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Buat Offering Baru
    </a>
</div>

{{-- Filter --}}
<div class="admin-card" style="margin-bottom:20px;">
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:1;min-width:200px;">
            <label class="form-label">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nomor, klien, paket..." class="form-control">
        </div>
        <div style="min-width:150px;">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="">Semua Status</option>
                @foreach($statuses as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;gap:8px;">
            <button type="submit" class="admin-btn admin-btn--primary">Filter</button>
            <a href="{{ route('admin.offerings.index') }}" class="admin-btn admin-btn--secondary">Reset</a>
        </div>
    </form>
</div>

<div class="admin-card">
    @if($offerings->isEmpty())
        <div style="text-align:center;padding:60px 20px;color:var(--text-muted);">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 16px;display:block;opacity:0.4;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <p>Belum ada Offering. <a href="{{ route('admin.offerings.create') }}" style="color:var(--teal);">Buat yang pertama</a></p>
        </div>
    @else
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>No. Offering</th>
                        <th>Klien / Paket</th>
                        <th>Tanggal</th>
                        <th>Peserta</th>
                        <th>Grand Total</th>
                        <th>Status</th>
                        <th>Invoice</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($offerings as $off)
                    @php $badge = $off->status_badge; @endphp
                    <tr>
                        <td>
                            <code style="font-size:0.8rem;color:var(--teal);">{{ $off->offering_number }}</code>
                        </td>
                        <td>
                            <div style="font-weight:600;">{{ $off->package_name }}</div>
                            <div style="font-size:0.8rem;color:var(--text-muted);">{{ $off->client_name }}</div>
                        </td>
                        <td style="font-size:0.85rem;color:var(--text-muted);">{{ $off->date_duration ?? '-' }}</td>
                        <td style="text-align:center;">{{ number_format($off->participants) }}</td>
                        <td>
                            <div style="font-weight:700;color:var(--teal);">{{ $off->total_amount_formatted }}</div>
                            <div style="font-size:0.78rem;color:var(--text-muted);">{{ $off->price_per_pack_formatted }}/pax</div>
                        </td>
                        <td><span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span></td>
                        <td>
                            @if($off->invoices_count ?? $off->invoices->count())
                                <span class="badge badge-success">{{ $off->invoices->count() }} Invoice</span>
                            @else
                                <span style="color:var(--text-muted);font-size:0.8rem;">—</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                <a href="{{ route('admin.offerings.show', $off) }}" class="admin-btn admin-btn--secondary admin-btn--sm">Lihat</a>
                                @if(in_array($off->status, ['draft','sent','revised']))
                                    <a href="{{ route('admin.offerings.edit', $off) }}" class="admin-btn admin-btn--secondary admin-btn--sm">Edit</a>
                                @endif
                                @if($off->status === 'approved')
                                    <a href="{{ route('admin.offerings.create-invoice', $off) }}" class="admin-btn admin-btn--primary admin-btn--sm">Buat Invoice</a>
                                @endif
                                <a href="{{ route('admin.offerings.proposal-pdf', $off) }}" class="admin-btn admin-btn--secondary admin-btn--sm" target="_blank">PDF</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $offerings->links() }}</div>
    @endif
</div>
@endsection
