@extends('admin.layout')

@section('title', 'Keuangan')

@push('styles')
<style>
/* ─── PIN Lock Screen ─────────────────────────────── */
.pin-overlay {
    position: fixed; inset: 0; z-index: 1000;
    background: radial-gradient(ellipse at 60% 30%, #0d2a3a 0%, #060d18 100%);
    display: flex; align-items: center; justify-content: center;
}
.pin-card {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(20,184,166,0.18);
    border-radius: 24px;
    padding: 56px 48px;
    width: 100%; max-width: 420px;
    text-align: center;
    backdrop-filter: blur(20px);
    box-shadow: 0 32px 80px rgba(0,0,0,0.5), 0 0 0 1px rgba(20,184,166,0.08);
    animation: pinSlideIn 0.4s cubic-bezier(.22,.68,0,1.2);
}
@keyframes pinSlideIn {
    from { opacity: 0; transform: translateY(30px) scale(0.96); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
.pin-lock-icon {
    width: 72px; height: 72px;
    background: linear-gradient(135deg, rgba(20,184,166,0.2), rgba(20,184,166,0.05));
    border: 1px solid rgba(20,184,166,0.3);
    border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 24px;
    font-size: 2rem;
}
.pin-title { font-size: 1.5rem; font-weight: 800; color: #f1f5f9; margin-bottom: 6px; }
.pin-subtitle { color: #64748b; font-size: 0.875rem; margin-bottom: 32px; }
.pin-dots {
    display: flex; justify-content: center; gap: 12px; margin-bottom: 24px;
}
.pin-dot {
    width: 14px; height: 14px;
    border-radius: 50%;
    border: 2px solid rgba(20,184,166,0.4);
    background: transparent;
    transition: all 0.2s;
}
.pin-dot.filled { background: var(--teal); border-color: var(--teal); box-shadow: 0 0 10px rgba(20,184,166,0.5); }
.pin-input-hidden {
    position: absolute; opacity: 0; pointer-events: none; width: 1px; height: 1px;
}
.pin-keypad {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 12px; margin-bottom: 20px;
}
.pin-key {
    padding: 16px; border-radius: 12px;
    background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08);
    color: #f1f5f9; font-size: 1.2rem; font-weight: 600; cursor: pointer;
    transition: all 0.15s; user-select: none;
}
.pin-key:hover { background: rgba(20,184,166,0.15); border-color: rgba(20,184,166,0.3); }
.pin-key:active { transform: scale(0.93); }
.pin-key--del { color: #f87171; }
.pin-error {
    background: rgba(239,68,68,0.1);
    border: 1px solid rgba(239,68,68,0.25);
    border-radius: 10px;
    padding: 10px 16px;
    color: #ef4444; font-size: 0.875rem; margin-bottom: 12px;
}
.pin-submit-btn {
    width: 100%; padding: 14px;
    background: linear-gradient(135deg, #14b8a6, #0d9488);
    border: none; border-radius: 12px;
    color: #fff; font-size: 1rem; font-weight: 700;
    cursor: pointer; transition: all 0.2s;
    box-shadow: 0 4px 20px rgba(20,184,166,0.3);
}
.pin-submit-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(20,184,166,0.4); }
.pin-submit-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

/* ─── Dashboard Keuangan ──────────────────────────── */
.finance-stat-card {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: 18px;
    padding: 26px 28px;
    position: relative; overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}
.finance-stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 40px rgba(0,0,0,0.25); }
.finance-stat-card::before {
    content: '';
    position: absolute; top: -40px; right: -30px;
    width: 120px; height: 120px;
    border-radius: 50%;
    opacity: 0.07;
}
.fsc-gross::before  { background: #3b82f6; }
.fsc-net::before    { background: #10b981; }
.fsc-margin::before { background: #f59e0b; }
.fsc-cash::before   { background: #14b8a6; }
.fsc-icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; margin-bottom: 14px;
}
.fsc-gross  .fsc-icon { background: rgba(59,130,246,0.15);  color: #60a5fa; }
.fsc-net    .fsc-icon { background: rgba(16,185,129,0.15);  color: #34d399; }
.fsc-margin .fsc-icon { background: rgba(245,158,11,0.15);  color: #fbbf24; }
.fsc-cash   .fsc-icon { background: rgba(20,184,166,0.15);  color: #2dd4bf; }
.fsc-value { font-size: 1.55rem; font-weight: 800; color: #f1f5f9; line-height: 1; margin-bottom: 6px; }
.fsc-label { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.07em; color: #64748b; font-weight: 600; }
.fsc-sub   { font-size: 0.8rem; color: #475569; margin-top: 6px; }

/* Charts */
.chart-card {
    background: var(--glass-bg); border: 1px solid var(--glass-border);
    border-radius: 18px; padding: 28px;
}
.chart-title { font-size: 1rem; font-weight: 700; color: #f1f5f9; margin-bottom: 4px; }
.chart-sub   { font-size: 0.8rem; color: #64748b; margin-bottom: 20px; }

/* Ledger */
.ledger-type-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 700;
}
.ledger-in  { background: rgba(34,197,94,0.12);  color: #22c55e; }
.ledger-out { background: rgba(239,68,68,0.12);  color: #ef4444; }

/* Modals */
.modal-overlay {
    display: none; position: fixed; inset: 0; z-index: 500;
    background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);
    align-items: center; justify-content: center;
}
.modal-overlay.active { display: flex; }
.modal-box {
    background: #0f1e35; border: 1px solid rgba(255,255,255,0.1);
    border-radius: 20px; padding: 36px; width: 100%; max-width: 480px;
    animation: pinSlideIn 0.3s ease;
}
.modal-title { font-size: 1.1rem; font-weight: 800; color: #f1f5f9; margin-bottom: 20px; }

/* Section Title */
.section-title {
    font-size: 1rem; font-weight: 700; color: #f1f5f9;
    display: flex; align-items: center; gap: 10px; margin-bottom: 18px;
}
.section-title-bar { width: 4px; height: 20px; background: var(--teal); border-radius: 4px; }
</style>
@endpush

@section('content')

{{-- ══════════════════════════════════════════════════════════════
     PIN LOCK SCREEN — tampil jika belum terverifikasi
══════════════════════════════════════════════════════════════ --}}
@if(!$pinVerified)
<div class="pin-overlay" id="pinOverlay">
    <div class="pin-card">
        <div class="pin-lock-icon">🔐</div>
        <div class="pin-title">Menu Keuangan</div>
        <div class="pin-subtitle">Masukkan PIN untuk mengakses laporan keuangan travine.id</div>

        @if($errors->has('pin'))
            <div class="pin-error">⚠️ {{ $errors->first('pin') }}</div>
        @endif
        @if(session('success'))
            <div style="background:rgba(20,184,166,0.1);border:1px solid rgba(20,184,166,0.3);border-radius:10px;padding:10px 16px;color:var(--teal);font-size:0.875rem;margin-bottom:12px;">
                ✓ {{ session('success') }}
            </div>
        @endif

        <div class="pin-dots" id="pinDots">
            <div class="pin-dot" id="d0"></div>
            <div class="pin-dot" id="d1"></div>
            <div class="pin-dot" id="d2"></div>
            <div class="pin-dot" id="d3"></div>
            <div class="pin-dot" id="d4"></div>
            <div class="pin-dot" id="d5"></div>
        </div>

        <form method="POST" action="{{ route('admin.expenditures.verify-pin') }}" id="pinForm">
            @csrf
            <input type="hidden" name="pin" id="pinValue">
        </form>

        <div class="pin-keypad">
            @foreach(['1','2','3','4','5','6','7','8','9','','0','⌫'] as $key)
                @if($key === '')
                    <div></div>
                @elseif($key === '⌫')
                    <button class="pin-key pin-key--del" type="button" onclick="pinDelete()">⌫</button>
                @else
                    <button class="pin-key" type="button" onclick="pinPress('{{ $key }}')">{{ $key }}</button>
                @endif
            @endforeach
        </div>

        <button class="pin-submit-btn" id="pinSubmitBtn" disabled onclick="submitPin()">
            🔓 Buka Menu
        </button>
    </div>
</div>

@push('scripts')
<script>
let pinVal = '';
const MAX = 6;

function pinPress(digit) {
    if (pinVal.length >= MAX) return;
    pinVal += digit;
    updateDots();
    if (pinVal.length === MAX) {
        setTimeout(submitPin, 200);
    }
}

function pinDelete() {
    pinVal = pinVal.slice(0, -1);
    updateDots();
}

function updateDots() {
    for (let i = 0; i < MAX; i++) {
        document.getElementById('d' + i).classList.toggle('filled', i < pinVal.length);
    }
    document.getElementById('pinSubmitBtn').disabled = pinVal.length < 4;
}

function submitPin() {
    if (pinVal.length < 4) return;
    document.getElementById('pinValue').value = pinVal;
    document.getElementById('pinForm').submit();
}

// Keyboard support
document.addEventListener('keydown', function(e) {
    if (e.key >= '0' && e.key <= '9') pinPress(e.key);
    else if (e.key === 'Backspace') pinDelete();
    else if (e.key === 'Enter') submitPin();
});
</script>
@endpush

@else

{{-- ══════════════════════════════════════════════════════════════
     DASHBOARD KEUANGAN
══════════════════════════════════════════════════════════════ --}}
<div class="admin-topbar">
    <div>
        <h1 style="display:flex;align-items:center;gap:10px;">
            💰 Laporan Keuangan
            <span style="background:rgba(20,184,166,0.15);border:1px solid rgba(20,184,166,0.3);color:var(--teal);font-size:0.65rem;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:0.1em;text-transform:uppercase;">PRIVAT</span>
        </h1>
        <p style="color:#64748b;font-size:0.85rem;margin-top:4px;">Laporan pendapatan & manajemen kas travine.id</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <button onclick="document.getElementById('changePinModal').classList.add('active')" class="admin-btn admin-btn--secondary" style="gap:8px;">
            🔑 Ganti PIN
        </button>
        <form method="POST" action="{{ route('admin.expenditures.lock') }}" style="display:inline;">
            @csrf
            <button type="submit" class="admin-btn admin-btn--danger" style="gap:8px;">
                🔒 Kunci Menu
            </button>
        </form>
    </div>
</div>

{{-- ── Stat Cards ────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:28px;">
    <div class="finance-stat-card fsc-gross">
        <div class="fsc-icon">💳</div>
        <div class="fsc-value">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</div>
        <div class="fsc-label">Pendapatan Kotor</div>
        <div class="fsc-sub">Booking & Project Invoice lunas</div>
    </div>
    <div class="finance-stat-card fsc-net">
        <div class="fsc-icon">💵</div>
        <div class="fsc-value">Rp {{ number_format($netRevenue, 0, ',', '.') }}</div>
        <div class="fsc-label">Pendapatan Bersih</div>
        <div class="fsc-sub">Setelah dikurangi HPP & modal vendor</div>
    </div>
    <div class="finance-stat-card fsc-margin">
        <div class="fsc-icon">📈</div>
        <div class="fsc-value">Rp {{ number_format($totalMargin, 0, ',', '.') }}</div>
        <div class="fsc-label">Total Margin</div>
        <div class="fsc-sub">Bersih − komisi partner (Rp {{ number_format($totalCommission, 0, ',', '.') }})</div>
    </div>
    <div class="finance-stat-card fsc-cash">
        <div class="fsc-icon">🏦</div>
        <div class="fsc-value">Rp {{ number_format($cashBalance, 0, ',', '.') }}</div>
        <div class="fsc-label">Saldo Kas travine.id</div>
        <div class="fsc-sub">Termasuk otomatis dari margin</div>
    </div>
</div>

{{-- ── Charts ────────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:28px;">
    <div class="chart-card">
        <div class="chart-title">📊 Grafik Pendapatan Bulanan</div>
        <div class="chart-sub">12 bulan terakhir — Pendapatan Kotor, Bersih & Margin</div>
        <canvas id="revenueChart" height="260"></canvas>
    </div>
    <div class="chart-card">
        <div class="chart-title">📦 Jumlah Booking/Bulan</div>
        <div class="chart-sub">Booking terkonfirmasi per bulan</div>
        <canvas id="bookingChart" height="260"></canvas>
    </div>
</div>

{{-- ── Tambah Mutasi Kas ─────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;margin-bottom:28px;align-items:start;">

    {{-- Form Tambah Kas --}}
    <div class="admin-card">
        <div class="section-title">
            <div class="section-title-bar"></div>
            <span>Tambah Mutasi Kas</span>
        </div>
        <form method="POST" action="{{ route('admin.expenditures.ledger') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Jenis Transaksi</label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <label class="ledger-type-option" id="opt-in" onclick="setType('in')"
                        style="padding:14px;border-radius:12px;border:2px solid rgba(34,197,94,0.3);background:rgba(34,197,94,0.05);cursor:pointer;text-align:center;transition:all .2s;">
                        <div style="font-size:1.4rem;">💚</div>
                        <div style="font-size:0.85rem;font-weight:700;color:#22c55e;">Kas Masuk</div>
                    </label>
                    <label class="ledger-type-option" id="opt-out" onclick="setType('out')"
                        style="padding:14px;border-radius:12px;border:2px solid rgba(255,255,255,0.08);background:rgba(255,255,255,0.02);cursor:pointer;text-align:center;transition:all .2s;">
                        <div style="font-size:1.4rem;">❤️</div>
                        <div style="font-size:0.85rem;font-weight:700;color:#f87171;">Kas Keluar</div>
                    </label>
                </div>
                <input type="hidden" name="type" id="typeInput" value="in">
            </div>

            <div class="form-group">
                <label class="form-label">Nominal (Rp)</label>
                <input type="number" name="amount" class="form-control" placeholder="0" min="1" required
                    style="font-size:1.1rem;font-weight:700;">
            </div>

            <div class="form-group">
                <label class="form-label">Catatan / Keterangan <span style="color:#ef4444;">*</span></label>
                <textarea name="note" class="form-control" rows="3"
                    placeholder="Contoh: Dana operasional, pembayaran vendor, dll." required></textarea>
            </div>

            <button type="submit" class="admin-btn admin-btn--primary" style="width:100%;justify-content:center;padding:12px;">
                💾 Simpan Transaksi
            </button>
        </form>
    </div>

    {{-- Riwayat Kas --}}
    <div class="admin-card">
        <div class="section-title" style="justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div class="section-title-bar"></div>
                <span>Riwayat Mutasi Kas</span>
            </div>
            <span style="font-size:0.8rem;color:#64748b;">{{ $ledgerHistory->total() }} transaksi</span>
        </div>

        @if($ledgerHistory->isEmpty())
            <div style="text-align:center;padding:40px;color:#475569;">
                <div style="font-size:2.5rem;margin-bottom:10px;">📭</div>
                <div>Belum ada transaksi kas.</div>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Tipe</th>
                            <th>Nominal</th>
                            <th>Keterangan</th>
                            <th>Sumber</th>
                            <th style="text-align:right;">Saldo Setelah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ledgerHistory as $entry)
                        <tr>
                            <td style="white-space:nowrap;color:#64748b;font-size:0.82rem;">
                                {{ $entry->created_at->format('d/m/Y H:i') }}<br>
                                <span style="font-size:0.75rem;color:#334155;">{{ $entry->created_by ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="ledger-type-badge {{ $entry->type === 'in' ? 'ledger-in' : 'ledger-out' }}">
                                    {{ $entry->type === 'in' ? '↑ Masuk' : '↓ Keluar' }}
                                </span>
                            </td>
                            <td style="font-weight:700;{{ $entry->type === 'in' ? 'color:#22c55e;' : 'color:#ef4444;' }}">
                                {{ $entry->type === 'in' ? '+' : '−' }} {{ $entry->amount_formatted }}
                            </td>
                            <td style="color:#cbd5e1;font-size:0.875rem;max-width:200px;">
                                {{ Str::limit($entry->note, 60) }}
                                @if($entry->booking)
                                    <br><span style="font-size:0.75rem;color:#14b8a6;">📋 {{ $entry->booking->booking_code }}</span>
                                @elseif($entry->projectInvoice)
                                    <br><a href="{{ route('admin.project-invoices.show', $entry->projectInvoice) }}" style="font-size:0.75rem;color:#818cf8;text-decoration:none;font-weight:600;">🧾 {{ $entry->projectInvoice->invoice_number }}</a>
                                @endif
                            </td>
                            <td>
                                @if($entry->source === 'booking')
                                    <span class="badge badge-info">Booking</span>
                                @elseif($entry->source === 'project_invoice')
                                    <span class="badge" style="background:rgba(99,102,241,0.18);color:#a5b4fc;border:1px solid rgba(99,102,241,0.35);">Invoice</span>
                                @elseif($entry->source === 'withdrawal')
                                    <span class="badge badge-warning">Withdrawal</span>
                                @else
                                    <span class="badge badge-muted">Manual</span>
                                @endif
                            </td>
                            <td style="text-align:right;font-weight:700;color:#94a3b8;white-space:nowrap;">
                                {{ $entry->balance_after_formatted }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                {{ $ledgerHistory->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ── Modal Ganti PIN ───────────────────────────────────────── --}}
<div class="modal-overlay" id="changePinModal" onclick="if(event.target===this)this.classList.remove('active')">
    <div class="modal-box">
        <div class="modal-title">🔑 Ganti PIN Keuangan</div>
        @error('current_pin')
            <div class="pin-error">{{ $message }}</div>
        @enderror
        <form method="POST" action="{{ route('admin.expenditures.change-pin') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">PIN Saat Ini</label>
                <div class="password-wrapper">
                    <input type="password" id="pin_current" name="current_pin" class="form-control" placeholder="Masukkan PIN lama" required>
                    <button type="button" class="password-toggle-btn" onclick="togglePassword('pin_current', this)" aria-label="Lihat PIN" title="Lihat/Sembunyikan PIN">
                        <svg class="eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg class="eye-slash" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">PIN Baru</label>
                <div class="password-wrapper">
                    <input type="password" id="pin_new" name="new_pin" class="form-control" placeholder="Minimal 4 digit" minlength="4" required>
                    <button type="button" class="password-toggle-btn" onclick="togglePassword('pin_new', this)" aria-label="Lihat PIN" title="Lihat/Sembunyikan PIN">
                        <svg class="eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg class="eye-slash" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi PIN Baru</label>
                <div class="password-wrapper">
                    <input type="password" id="pin_confirm" name="new_pin_confirmation" class="form-control" placeholder="Ulangi PIN baru" required>
                    <button type="button" class="password-toggle-btn" onclick="togglePassword('pin_confirm', this)" aria-label="Lihat PIN" title="Lihat/Sembunyikan PIN">
                        <svg class="eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg class="eye-slash" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                    </button>
                </div>
            </div>
            <div style="display:flex;gap:12px;">
                <button type="button" onclick="document.getElementById('changePinModal').classList.remove('active')"
                    class="admin-btn admin-btn--secondary" style="flex:1;justify-content:center;">Batal</button>
                <button type="submit" class="admin-btn admin-btn--primary" style="flex:1;justify-content:center;">Simpan PIN</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
// ── Type Toggle ──────────────────────────────────────────
function setType(type) {
    document.getElementById('typeInput').value = type;
    const optIn  = document.getElementById('opt-in');
    const optOut = document.getElementById('opt-out');
    if (type === 'in') {
        optIn.style.borderColor  = 'rgba(34,197,94,0.6)';
        optIn.style.background   = 'rgba(34,197,94,0.12)';
        optOut.style.borderColor = 'rgba(255,255,255,0.08)';
        optOut.style.background  = 'rgba(255,255,255,0.02)';
    } else {
        optOut.style.borderColor = 'rgba(239,68,68,0.6)';
        optOut.style.background  = 'rgba(239,68,68,0.12)';
        optIn.style.borderColor  = 'rgba(255,255,255,0.08)';
        optIn.style.background   = 'rgba(255,255,255,0.02)';
    }
}
setType('in'); // default

// ── Open changePinModal if errors exist ─────────────────
@if($errors->has('current_pin') || $errors->has('new_pin'))
    document.getElementById('changePinModal').classList.add('active');
@endif

// ── Chart.js ─────────────────────────────────────────────
const monthlyData = @json($monthlyData);

const commonOptions = {
    responsive: true,
    plugins: {
        legend: {
            labels: { color: '#94a3b8', font: { size: 11 }, boxWidth: 12 }
        },
        tooltip: {
            backgroundColor: '#0f2035',
            borderColor: 'rgba(20,184,166,0.3)',
            borderWidth: 1,
            titleColor: '#f1f5f9',
            bodyColor: '#94a3b8',
            callbacks: {
                label: function(ctx) {
                    if (ctx.dataset.label === 'Booking') return ' ' + ctx.raw + ' booking';
                    return ' Rp ' + ctx.raw.toLocaleString('id-ID');
                }
            }
        }
    },
    scales: {
        x: {
            ticks: { color: '#475569', font: { size: 10 } },
            grid:  { color: 'rgba(255,255,255,0.04)' }
        },
        y: {
            ticks: {
                color: '#475569', font: { size: 10 },
                callback: v => 'Rp ' + (v / 1000000).toFixed(1) + 'jt'
            },
            grid: { color: 'rgba(255,255,255,0.04)' }
        }
    }
};

// Revenue Chart
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: monthlyData.labels,
        datasets: [
            {
                label: 'Kotor',
                data: monthlyData.gross,
                borderColor: '#60a5fa',
                backgroundColor: 'rgba(96,165,250,0.08)',
                borderWidth: 2.5,
                pointRadius: 4,
                pointBackgroundColor: '#60a5fa',
                tension: 0.4,
                fill: true,
            },
            {
                label: 'Bersih',
                data: monthlyData.net,
                borderColor: '#34d399',
                backgroundColor: 'rgba(52,211,153,0.08)',
                borderWidth: 2.5,
                pointRadius: 4,
                pointBackgroundColor: '#34d399',
                tension: 0.4,
                fill: true,
            },
            {
                label: 'Margin',
                data: monthlyData.margin,
                borderColor: '#fbbf24',
                backgroundColor: 'rgba(251,191,36,0.08)',
                borderWidth: 2.5,
                pointRadius: 4,
                pointBackgroundColor: '#fbbf24',
                tension: 0.4,
                fill: true,
            }
        ]
    },
    options: commonOptions
});

// Booking Count Chart
new Chart(document.getElementById('bookingChart'), {
    type: 'bar',
    data: {
        labels: monthlyData.labels,
        datasets: [{
            label: 'Booking',
            data: monthlyData.bookingCount,
            backgroundColor: 'rgba(20,184,166,0.4)',
            borderColor: 'rgba(20,184,166,0.8)',
            borderWidth: 1.5,
            borderRadius: 8,
        }]
    },
    options: {
        ...commonOptions,
        scales: {
            x: { ticks: { color: '#475569', font: { size: 10 } }, grid: { color: 'rgba(255,255,255,0.04)' } },
            y: {
                ticks: { color: '#475569', font: { size: 10 }, stepSize: 1 },
                grid:  { color: 'rgba(255,255,255,0.04)' }
            }
        }
    }
});

function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    btn.classList.toggle('is-visible', isPass);
    btn.setAttribute('aria-label', isPass ? 'Sembunyikan' : 'Lihat');
}
</script>
@endpush

@endif
@endsection
