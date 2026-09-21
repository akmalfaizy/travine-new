@extends('admin.layout')
@section('title', 'Pengajuan Withdraw Komisi')
@section('content')
<div class="admin-topbar">
    <h1>💸 Pengajuan Withdraw Komisi Partner</h1>
</div>

@if(session('success'))
    <div class="admin-alert admin-alert--success">✓ {{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="admin-alert admin-alert--error">⚠️ {{ session('error') }}</div>
@endif

{{-- Filters --}}
<div class="admin-card" style="margin-bottom:24px;">
    <form method="GET" action="{{ route('admin.withdrawals.index') }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
        <input type="text" name="search" class="form-control" placeholder="Cari kode request, nama partner, no rekening..." value="{{ request('search') }}" style="max-width:340px;">
        <select name="status" class="form-control" style="max-width:180px;">
            <option value="">-- Semua Status --</option>
            <option value="requested" {{ request('status') === 'requested' ? 'selected' : '' }}>Menunggu Diproses</option>
            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Sedang Transfer</option>
            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>
        <button type="submit" class="admin-btn admin-btn--primary">Filter</button>
        @if(request('search') || request('status'))
            <a href="{{ route('admin.withdrawals.index') }}" class="admin-btn admin-btn--secondary">Reset</a>
        @endif
    </form>
</div>

<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Kode Request</th>
                <th>Partner</th>
                <th>Nominal Cair</th>
                <th>Rekening Tujuan Transfer</th>
                <th>Tanggal Request</th>
                <th>Status</th>
                <th>Aksi Prosedur</th>
            </tr>
        </thead>
        <tbody>
        @forelse($withdrawals as $w)
            <tr>
                <td>
                    <code style="background:rgba(20,184,166,0.15); color:var(--teal); padding:4px 8px; border-radius:6px; font-weight:700;">{{ $w->withdrawal_code }}</code>
                </td>
                <td>
                    <div style="font-weight:700; color:var(--text-primary);">{{ $w->partner?->name }}</div>
                    <small style="color:var(--text-muted);">{{ $w->partner?->email }}</small>
                </td>
                <td>
                    <span style="font-weight:800; font-size:1.1rem; color:var(--orange, #F97316);">Rp {{ number_format($w->amount, 0, ',', '.') }}</span>
                </td>
                <td style="font-size:0.85rem;">
                    <div style="font-weight:700; color:var(--teal);">{{ $w->bank_name }} - {{ $w->bank_account_number }}</div>
                    <div style="color:var(--text-muted); font-size:0.78rem;">a.n {{ $w->bank_account_name }}</div>
                </td>
                <td style="color:var(--text-muted); font-size:0.85rem;">
                    {{ $w->created_at->format('d M Y, H:i') }}
                </td>
                <td>
                    @if($w->status === 'requested')
                        <span class="badge badge-warning">⌛ Menunggu Transfer</span>
                    @elseif(in_array($w->status, ['approved', 'processing']))
                        <span class="badge badge-info">🔄 Sedang Transfer</span>
                    @elseif($w->status === 'paid')
                        <span class="badge badge-success">✓ Sudah Dibayar</span>
                    @else
                        <span class="badge badge-danger">❌ Ditolak</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.withdrawals.show', $w) }}" class="admin-btn admin-btn--primary admin-btn--sm">
                        Proses Transfer / Detail
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="text-align:center; color:var(--text-muted); padding:40px;">
                    Belum ada pengajuan penarikan komisi.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:20px;">
        {{ $withdrawals->links() }}
    </div>
</div>
@endsection
