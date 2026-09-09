@extends('layouts.app')

@section('content')
<section class="section" style="padding-top:120px;min-height:100vh;">
    <div class="container">
        {{-- Header --}}
        <div class="partner-dash__header">
            <div>
                <h1 class="partner-dash__title">Pengajuan & Penarikan Komisi</h1>
                <p class="partner-dash__subtitle">Tarik saldo komisi yang tersedia secara langsung ke rekening bank Anda.</p>
            </div>
            <div class="partner-dash__actions">
                <a href="{{ route('partner.dashboard') }}" class="btn btn--outline" style="font-size:0.85rem;padding:10px 20px;">← Dashboard</a>
            </div>
        </div>

        @include('partner.nav')

        @if(session('success'))
            <div style="background:rgba(20,184,166,0.15); border:1px solid rgba(20,184,166,0.3); color:#2dd4bf; padding:14px 20px; border-radius:10px; margin-bottom:24px; font-weight:600;">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background:rgba(248,113,113,0.15); border:1px solid rgba(248,113,113,0.3); color:#f87171; padding:14px 20px; border-radius:10px; margin-bottom:24px; font-weight:600;">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background:rgba(248,113,113,0.15); border:1px solid rgba(248,113,113,0.3); color:#f87171; padding:14px 20px; border-radius:10px; margin-bottom:24px;">
                <ul style="margin:0; padding-left:20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="partner-grid-withdraw">
            {{-- Form Withdrawal Request --}}
            <div class="partner-auth-card" style="padding:28px;">
                <h3 style="font-size:1.15rem; font-weight:700; color:var(--text-primary); margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                    💸 Form Penarikan Komisi
                </h3>

                <div style="background:rgba(20,184,166,0.1); border:1px dashed rgba(20,184,166,0.25); padding:16px; border-radius:10px; margin-bottom:20px;">
                    <span style="font-size:0.8rem; color:var(--text-muted); display:block;">Saldo Komisi Tersedia:</span>
                    <div style="font-size:1.6rem; font-weight:850; color:var(--teal,#14B8A6); margin-top:2px;">
                        Rp {{ number_format($partner->available_commission, 0, ',', '.') }}
                    </div>
                </div>

                @if(!$partner->isProfileComplete())
                    <div style="background:rgba(249,115,22,0.15); border:1px solid rgba(249,115,22,0.3); color:#fb923c; padding:14px; border-radius:8px; font-size:0.85rem; margin-bottom:16px;">
                        ⚠️ Silakan isi data rekening bank terlebih dahulu.
                    </div>
                    <a href="{{ route('partner.profile') }}" class="btn btn--primary" style="width:100%; text-align:center; display:block;">
                        Isi Rekening Bank
                    </a>
                @else
                    <div style="background:var(--bg-alt); border:1px solid var(--border-color); padding:14px; border-radius:8px; font-size:0.825rem; margin-bottom:20px; line-height:1.5; color:var(--text-primary);">
                        <div><strong>Tujuan Transfer:</strong></div>
                        <div style="color:var(--teal,#14B8A6); font-weight:700; margin-top:2px;">{{ $partner->bank_name }} - {{ $partner->bank_account_number }}</div>
                        <div style="color:var(--text-muted);">a.n {{ $partner->bank_account_name }}</div>
                        <a href="{{ route('partner.profile') }}" style="color:var(--teal); font-size:0.78rem; text-decoration:underline; display:inline-block; margin-top:6px;">Ubah Rekening</a>
                    </div>

                    <form method="POST" action="{{ route('partner.withdrawals.store') }}">
                        @csrf

                        <div class="form-group" style="margin-bottom:20px;">
                            <label class="form-label" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600; color:var(--text-primary);">Nominal Penarikan (Rp) *</label>
                            <input type="number" 
                                   name="amount" 
                                   class="form-control" 
                                   placeholder="Min. 100000" 
                                   min="{{ $minWithdrawal }}" 
                                   max="{{ $partner->available_commission }}" 
                                   value="{{ old('amount', $partner->available_commission >= $minWithdrawal ? $partner->available_commission : '') }}"
                                   required 
                                   style="font-size:1.1rem; font-weight:700;">
                            <small style="color:var(--text-muted); font-size:0.78rem; display:block; margin-top:4px;">Minimal penarikan: Rp {{ number_format($minWithdrawal, 0, ',', '.') }}</small>
                        </div>

                        <button type="submit" 
                                class="btn btn--primary" 
                                style="width:100%; padding:14px; border-radius:8px; font-weight:700; font-size:0.95rem;"
                                {{ $partner->available_commission < $minWithdrawal ? 'disabled style=opacity:0.5;cursor:not-allowed;' : '' }}>
                            🚀 Kirim Pengajuan Penarikan
                        </button>
                    </form>
                @endif
            </div>

            {{-- History Withdrawals --}}
            <div class="partner-auth-card" style="padding:28px;">
                <h3 style="font-size:1.15rem; font-weight:700; color:var(--text-primary); margin-bottom:20px;">
                    📜 Riwayat Penarikan Komisi
                </h3>

                @if($withdrawals->count() > 0)
                    <div style="overflow-x:auto;">
                        <table class="partner-dash__table" style="width:100%; border-collapse:collapse; font-size:0.875rem;">
                            <thead>
                                <tr style="border-bottom:1px solid var(--border-color); text-align:left; color:var(--text-muted);">
                                    <th style="padding:12px 8px;">Kode Request</th>
                                    <th style="padding:12px 8px;">Nominal</th>
                                    <th style="padding:12px 8px;">Bank Tujuan</th>
                                    <th style="padding:12px 8px;">Tanggal</th>
                                    <th style="padding:12px 8px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($withdrawals as $w)
                                    <tr style="border-bottom:1px solid var(--border-color);">
                                        <td style="padding:12px 8px; font-family:monospace; font-weight:700; color:var(--teal);">{{ $w->withdrawal_code }}</td>
                                        <td style="padding:12px 8px; font-weight:700; color:var(--text-primary);">Rp {{ number_format($w->amount, 0, ',', '.') }}</td>
                                        <td style="padding:12px 8px; color:var(--text-muted);">
                                            {{ $w->bank_name }} - {{ $w->bank_account_number }}
                                            <small style="display:block; font-size:0.75rem;">a.n {{ $w->bank_account_name }}</small>
                                        </td>
                                        <td style="padding:12px 8px; color:var(--text-muted);">{{ $w->created_at->format('d M Y, H:i') }}</td>
                                        <td style="padding:12px 8px;">
                                            @if($w->status === 'requested')
                                                <span class="partner-dash__badge partner-dash__badge--pending">Menunggu Diproses</span>
                                            @elseif(in_array($w->status, ['approved', 'processing']))
                                                <span class="partner-dash__badge partner-dash__badge--processing">Sedang Transfer</span>
                                            @elseif($w->status === 'paid')
                                                <span class="partner-dash__badge partner-dash__badge--paid">✓ Cair / Dibayar</span>
                                            @else
                                                <span class="partner-dash__badge partner-dash__badge--rejected">Ditolak</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top:20px;">
                        {{ $withdrawals->links() }}
                    </div>
                @else
                    <div style="text-align:center; padding:40px 20px; color:var(--text-muted);">
                        <div style="font-size:3rem; margin-bottom:12px;">💸</div>
                        <p style="font-size:0.95rem; font-weight:600;">Belum Ada Riwayat Penarikan</p>
                        <p style="font-size:0.85rem;">Saldo komisi Anda yang terkumpul nantinya dapat ditarik kapan saja melalui form di samping.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
