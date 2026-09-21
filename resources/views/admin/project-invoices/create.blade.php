@extends('admin.layout')
@section('title', 'Buat Invoice dari Offering — ' . $offering->offering_number)
@section('content')

<div class="admin-topbar">
    <div>
        <h1>Buat Project Invoice</h1>
        <p style="color:var(--text-muted);font-size:0.85rem;margin-top:4px;">
            Berdasarkan Offering: <code style="color:var(--teal);">{{ $offering->offering_number }}</code>
        </p>
    </div>
    <a href="{{ route('admin.offerings.show', $offering) }}" class="admin-btn admin-btn--secondary">← Kembali</a>
</div>

@if(!$offering->invoices->isEmpty())
<div class="admin-alert admin-alert--error" style="margin-bottom:20px;">
    ⚠️ Offering ini sudah memiliki {{ $offering->invoices->count() }} Invoice. Anda dapat membuat invoice tambahan, tetapi pastikan tidak ada duplikasi.
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start;">

    {{-- LEFT: Invoice form --}}
    <div>
        <form method="POST" action="{{ route('admin.project-invoices.store') }}">
        @csrf
        <input type="hidden" name="offering_id" value="{{ $offering->id }}">

        <div class="admin-card" style="margin-bottom:20px;">
            <h3 style="font-weight:700;margin-bottom:20px;">Data Invoice</h3>
            <div class="admin-grid admin-grid--2">
                <div class="form-group">
                    <label class="form-label">Tanggal Terbit *</label>
                    <input type="date" name="issue_date" class="form-control" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Jatuh Tempo *</label>
                    <input type="date" name="due_date" class="form-control" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email Penerima Tagihan (Klien)</label>
                <input type="email" name="client_email" class="form-control" value="{{ old('client_email', $offering->client_email) }}" placeholder="finance@perusahaan.com">
                <div style="font-size:0.78rem;color:var(--text-muted);margin-top:4px;">Digunakan untuk pengiriman invoice dan bukti pembayaran via email</div>
            </div>

            <div class="form-group">
                <label class="form-label">PPh (%)</label>
                <input type="number" name="pph_percentage" id="pphPct" class="form-control" value="{{ old('pph_percentage', 2) }}" min="0" max="100" step="0.01" oninput="recalcInvoice()">
                <div style="font-size:0.78rem;color:var(--text-muted);margin-top:4px;">Default: 2%. PPh dipotong dari Grand Total oleh pihak klien.</div>
            </div>

            <div class="form-group">
                <label class="form-label" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="include_pph_deduction" value="1" {{ old('include_pph_deduction') ? 'checked' : '' }}>
                    Tampilkan potongan PPh pada invoice (klien memotong PPh)
                </label>
            </div>

            <div class="form-group">
                <label class="form-label">Informasi Bank (Opsional)</label>
                <div class="admin-grid admin-grid--3" style="gap:10px;">
                    <input type="text" name="bank_name" class="form-control" placeholder="Nama Bank" value="{{ old('bank_name') }}">
                    <input type="text" name="bank_account" class="form-control" placeholder="No. Rekening" value="{{ old('bank_account') }}">
                    <input type="text" name="bank_holder" class="form-control" placeholder="Atas Nama" value="{{ old('bank_holder') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Invoice (Opsional)</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Syarat pembayaran, informasi tambahan...">{{ old('notes') }}</textarea>
            </div>
        </div>

        {{-- Calculated Summary --}}
        <div class="admin-card" style="background:rgba(20,184,166,0.06);border-color:rgba(20,184,166,0.2);margin-bottom:20px;">
            <h3 style="font-weight:700;margin-bottom:16px;">Kalkulasi Invoice</h3>
            <div style="display:flex;flex-direction:column;gap:0;">
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(255,255,255,0.06);">
                    <span style="color:var(--text-secondary);">Biaya Produksi</span>
                    <span style="font-family:monospace;">Rp {{ number_format($offering->production_cost, 0, ',', '.') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(255,255,255,0.06);">
                    <span style="color:var(--text-secondary);">Biaya Lain-Lain ({{ $offering->other_cost_percentage }}%)</span>
                    <span style="font-family:monospace;">Rp {{ number_format($offering->other_cost, 0, ',', '.') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(255,255,255,0.06);">
                    <span style="color:var(--text-secondary);">Management Fee ({{ $offering->management_fee_percentage }}%)</span>
                    <span style="font-family:monospace;">Rp {{ number_format($offering->management_fee, 0, ',', '.') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:14px 0;border-bottom:2px solid var(--teal);border-top:1px solid var(--glass-border);font-size:1.05rem;font-weight:800;">
                    <span>GROSS TOTAL</span>
                    <span style="color:var(--teal);font-family:monospace;">{{ $offering->total_amount_formatted }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(255,255,255,0.06);">
                    <span style="color:#ef4444;">PPh (<span id="pphPctDisplay">2</span>%)</span>
                    <span style="color:#ef4444;font-family:monospace;" id="pphAmountDisplay">Rp 0</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:12px 0;font-size:1rem;font-weight:800;">
                    <span>NET AMOUNT (diterima)</span>
                    <span style="color:var(--teal);font-family:monospace;" id="netAmountDisplay">Rp {{ number_format($offering->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;">
            <a href="{{ route('admin.offerings.show', $offering) }}" class="admin-btn admin-btn--secondary">Batal</a>
            <button type="submit" class="admin-btn admin-btn--primary" style="min-width:160px;">
                🧾 Buat Invoice Sekarang
            </button>
        </div>
        </form>
    </div>

    {{-- RIGHT: Offering summary --}}
    <div>
        <div class="admin-card">
            <h3 style="font-weight:700;margin-bottom:16px;">Data Offering (Otomatis Tersalin)</h3>
            <div style="display:flex;flex-direction:column;gap:12px;">
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:2px;">Client</div>
                    <div style="font-weight:700;">{{ $offering->client_name }}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:2px;">PIC Client</div>
                    <div>{{ $offering->pic_client ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:2px;">Nama Paket</div>
                    <div style="font-weight:600;">{{ $offering->package_name }}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:2px;">Tanggal & Durasi</div>
                    <div>{{ $offering->date_duration ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:2px;">Peserta</div>
                    <div>{{ number_format($offering->participants) }} orang</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:2px;">Price Per Pack</div>
                    <div style="font-weight:700;color:var(--teal);">{{ $offering->price_per_pack_formatted }}</div>
                </div>
            </div>
            <hr style="border-color:var(--glass-border);margin:16px 0;">
            <div style="font-size:0.8rem;color:var(--text-muted);">
                <strong style="color:var(--text-secondary);">ℹ️ Catatan:</strong><br>
                Semua item budget akan tersalin otomatis sebagai snapshot ke Invoice. Perubahan pada Offering setelah invoice dibuat tidak akan mempengaruhi invoice ini.
            </div>
        </div>

        {{-- Item preview --}}
        <div class="admin-card" style="margin-top:16px;max-height:400px;overflow-y:auto;">
            <h4 style="font-weight:700;margin-bottom:12px;font-size:0.9rem;">Preview Item ({{ $offering->categories->sum(fn($c) => $c->items->count()) }} item)</h4>
            @foreach($offering->categories as $cat)
                <div style="margin-bottom:12px;">
                    <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;color:var(--teal);margin-bottom:6px;">{{ $cat->name }}</div>
                    @foreach($cat->items as $item)
                    <div style="display:flex;justify-content:space-between;font-size:0.82rem;padding:4px 0;border-bottom:1px solid rgba(255,255,255,0.04);">
                        <span style="color:var(--text-secondary);">{{ $item->component }}</span>
                        <span style="font-family:monospace;font-weight:600;">{{ $item->total_cost_formatted }}</span>
                    </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
const grossTotal = {{ $offering->total_amount }};
function recalcInvoice() {
    const pphPct = parseFloat(document.getElementById('pphPct')?.value) || 0;
    const pphAmt = grossTotal * (pphPct / 100);
    const netAmt = grossTotal - pphAmt;
    const fmt = v => 'Rp ' + Math.round(v).toLocaleString('id-ID');
    document.getElementById('pphPctDisplay').textContent = pphPct;
    document.getElementById('pphAmountDisplay').textContent = fmt(pphAmt);
    document.getElementById('netAmountDisplay').textContent = fmt(netAmt);
}
recalcInvoice();
</script>
@endpush
@endsection
