@extends('admin.layout')
@section('title', 'Daftar Project Invoice — Admin')
@section('content')

<div class="admin-topbar">
    <div>
        <h1>Project Invoice</h1>
        <p style="color:var(--text-muted);font-size:0.85rem;margin-top:4px;">Invoice berbasis Offering / Production Cost</p>
    </div>
    <a href="{{ route('admin.offerings.index') }}" class="admin-btn admin-btn--secondary">
        📋 Daftar Offering
    </a>
</div>

{{-- Filter --}}
<div class="admin-card" style="margin-bottom:20px;">
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:1;min-width:200px;">
            <label class="form-label">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="No. Invoice, klien, project..." class="form-control">
        </div>
        <div style="min-width:150px;">
            <label class="form-label">Status Bayar</label>
            <select name="payment_status" class="form-control">
                <option value="">Semua</option>
                @foreach($statuses as $s)
                    <option value="{{ $s }}" {{ request('payment_status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;gap:8px;">
            <button type="submit" class="admin-btn admin-btn--primary">Filter</button>
            <a href="{{ route('admin.project-invoices.index') }}" class="admin-btn admin-btn--secondary">Reset</a>
        </div>
    </form>
</div>

<div class="admin-card">
    @if($invoices->isEmpty())
        <div style="text-align:center;padding:60px 20px;color:var(--text-muted);">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 16px;display:block;opacity:0.4;"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            <p>Belum ada Project Invoice. Buat Offering → Approve → Buat Invoice.</p>
            <a href="{{ route('admin.offerings.index') }}" class="admin-btn admin-btn--primary" style="margin-top:12px;">Ke Daftar Offering</a>
        </div>
    @else
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>No. Invoice</th>
                        <th>Offering</th>
                        <th>Klien / Project</th>
                        <th>Tgl Terbit</th>
                        <th>Jatuh Tempo</th>
                        <th>Gross Total</th>
                        <th>Terbayar</th>
                        <th>Sisa</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($invoices as $inv)
                    @php $ps = $inv->payment_status_badge; @endphp
                    <tr>
                        <td><code style="font-size:0.8rem;color:var(--teal);">{{ $inv->invoice_number }}</code></td>
                        <td>
                            @if($inv->offering)
                                <a href="{{ route('admin.offerings.show', $inv->offering) }}" style="color:var(--teal);font-size:0.8rem;">
                                    {{ $inv->offering->offering_number }}
                                </a>
                            @else
                                <span style="color:var(--text-muted);">-</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:600;">{{ $inv->project_name }}</div>
                            <div style="font-size:0.8rem;color:var(--text-muted);">{{ $inv->client_name }}</div>
                        </td>
                        <td style="font-size:0.85rem;">{{ $inv->issue_date->format('d M Y') }}</td>
                        <td style="font-size:0.85rem;{{ now()->gt($inv->due_date) && $inv->payment_status !== 'paid' ? 'color:#ef4444;font-weight:700;' : '' }}">
                            {{ $inv->due_date->format('d M Y') }}
                        </td>
                        <td style="font-family:monospace;font-weight:700;">{{ $inv->gross_total_formatted }}</td>
                        <td style="font-family:monospace;color:#22c55e;">{{ $inv->amount_paid_formatted }}</td>
                        <td style="font-family:monospace;{{ $inv->remaining_balance > 0 ? 'color:#ef4444;' : 'color:#22c55e;' }}">{{ $inv->remaining_balance_formatted }}</td>
                        <td><span class="badge {{ $ps['class'] }}">{{ $ps['label'] }}</span></td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('admin.project-invoices.show', $inv) }}" class="admin-btn admin-btn--secondary admin-btn--sm">Lihat</a>
                                <a href="{{ route('admin.project-invoices.pdf', $inv) }}" class="admin-btn admin-btn--secondary admin-btn--sm" target="_blank">PDF</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $invoices->links() }}</div>
    @endif
</div>
@endsection
