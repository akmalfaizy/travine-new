@extends('admin.layout')
@section('title', 'Detail Invoice — ' . $projectInvoice->invoice_number)
@section('content')

@php $ps = $projectInvoice->payment_status_badge; @endphp

<div class="admin-topbar">
    <div>
        <h1>{{ $projectInvoice->invoice_number }}</h1>
        <p style="color:var(--text-muted);font-size:0.85rem;margin-top:4px;">
            {{ $projectInvoice->project_name }} · {{ $projectInvoice->client_name }}
        </p>
    </div>
    <div style="display:flex;gap:8px;">
        <button type="button" onclick="openInvoiceEmailModal()" class="admin-btn admin-btn--primary">
            ✉️ Kirim via Email
        </button>
        <a href="{{ route('admin.project-invoices.pdf', $projectInvoice) }}" target="_blank" class="admin-btn admin-btn--secondary">📄 Download PDF</a>
        <a href="{{ route('admin.project-invoices.index') }}" class="admin-btn admin-btn--secondary">← Kembali</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;">

    {{-- LEFT: Invoice body --}}
    <div style="display:flex;flex-direction:column;gap:20px;">

        {{-- Invoice Header Info --}}
        <div class="admin-card">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;">
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">No. Invoice</div>
                    <code style="font-size:1rem;font-weight:800;color:var(--teal);">{{ $projectInvoice->invoice_number }}</code>
                    @if($projectInvoice->offering)
                    <div style="font-size:0.78rem;color:var(--text-muted);margin-top:4px;">
                        Ref Offering:
                        <a href="{{ route('admin.offerings.show', $projectInvoice->offering) }}" style="color:var(--teal);">{{ $projectInvoice->offering->offering_number }}</a>
                    </div>
                    @endif
                </div>
                <span class="badge {{ $ps['class'] }}" style="font-size:0.85rem;padding:6px 14px;">{{ $ps['label'] }}</span>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Client</div>
                    <div style="font-weight:700;">{{ $projectInvoice->client_name }}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">PIC Client</div>
                    <div>{{ $projectInvoice->pic_client ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Project</div>
                    <div style="font-weight:700;">{{ $projectInvoice->project_name }}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Kontak PIC</div>
                    <div>{{ $projectInvoice->pic_contact ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Email Klien</div>
                    <div style="color:var(--teal);font-weight:600;">{{ $projectInvoice->client_email ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Tgl Terbit</div>
                    <div>{{ $projectInvoice->issue_date->format('d M Y') }}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Jatuh Tempo</div>
                    <div style="{{ now()->gt($projectInvoice->due_date) && $projectInvoice->payment_status !== 'paid' ? 'color:#ef4444;font-weight:700;' : '' }}">
                        {{ $projectInvoice->due_date->format('d M Y') }}
                        @if(now()->gt($projectInvoice->due_date) && $projectInvoice->payment_status !== 'paid')
                            <span class="badge badge-danger" style="font-size:0.7rem;margin-left:4px;">JATUH TEMPO</span>
                        @endif
                    </div>
                </div>
            </div>
            @if($projectInvoice->description)
            <div style="margin-top:16px;padding:12px;background:rgba(255,255,255,0.04);border-radius:8px;">
                <div style="font-size:0.72rem;color:var(--text-muted);margin-bottom:4px;">Deskripsi</div>
                <div style="font-size:0.9rem;">{{ $projectInvoice->description }}</div>
            </div>
            @endif
        </div>

        {{-- Items Snapshot --}}
        <div class="admin-card">
            <h3 style="font-weight:700;margin-bottom:16px;">Item Invoice (Snapshot dari Offering)</h3>
            @php
                $itemsByCategory = $projectInvoice->items->groupBy('category_name');
            @endphp
            @forelse($itemsByCategory as $catName => $items)
                <div style="margin-bottom:20px;">
                    <div style="background:rgba(20,184,166,0.08);border-left:3px solid var(--teal);padding:8px 14px;font-weight:700;margin-bottom:0;border-radius:4px 4px 0 0;">{{ $catName }}</div>
                    <div style="overflow-x:auto;">
                        <table class="admin-table" style="border:1px solid var(--glass-border);border-top:none;">
                            <thead>
                                <tr>
                                    <th>Component</th>
                                    <th>Deskripsi</th>
                                    <th>Remark</th>
                                    <th style="text-align:center;">Type</th>
                                    <th style="text-align:right;">Qty</th>
                                    <th style="text-align:right;">Harga @</th>
                                    <th style="text-align:right;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td><strong>{{ $item->component }}</strong></td>
                                    <td style="font-size:0.85rem;color:var(--text-muted);">{{ $item->description ?? '-' }}</td>
                                    <td><span class="badge badge-info" style="font-size:0.72rem;">{{ $item->remark }}</span></td>
                                    <td style="text-align:center;"><span class="badge {{ $item->type === 'V' ? 'badge-info' : 'badge-warning' }}">{{ $item->type }}</span></td>
                                    <td style="text-align:right;">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                                    <td style="text-align:right;font-family:monospace;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td style="text-align:right;font-weight:700;font-family:monospace;color:var(--teal);">Rp {{ number_format($item->total_cost, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background:rgba(20,184,166,0.04);">
                                    <td colspan="6" style="text-align:right;font-size:0.8rem;color:var(--text-muted);">Subtotal {{ $catName }}</td>
                                    <td style="text-align:right;font-weight:700;font-family:monospace;">Rp {{ number_format($items->sum('total_cost'), 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @empty
                <p style="color:var(--text-muted);">Tidak ada item.</p>
            @endforelse

            {{-- Financial Summary --}}
            <div style="max-width:450px;margin-left:auto;margin-top:24px;border:1px solid rgba(20,184,166,0.25);border-radius:var(--radius-sm);overflow:hidden;">
                <div style="background:rgba(20,184,166,0.08);padding:10px 16px;font-weight:700;font-size:0.85rem;text-transform:uppercase;letter-spacing:0.05em;color:var(--teal);">Kalkulasi Biaya</div>
                <div style="display:flex;justify-content:space-between;padding:10px 16px;border-bottom:1px solid rgba(255,255,255,0.04);">
                    <span style="color:var(--text-secondary);">Biaya Produksi (Subtotal)</span>
                    <span style="font-family:monospace;">{{ $projectInvoice->subtotal_formatted }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 16px;border-bottom:1px solid rgba(255,255,255,0.04);">
                    <span style="color:var(--text-secondary);">Biaya Lain-Lain</span>
                    <span style="font-family:monospace;">{{ $projectInvoice->other_cost_formatted }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 16px;border-bottom:1px solid rgba(255,255,255,0.04);">
                    <span style="color:var(--text-secondary);">Management Fee</span>
                    <span style="font-family:monospace;">{{ $projectInvoice->management_fee_formatted }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:14px 16px;background:rgba(20,184,166,0.12);border-top:2px solid var(--teal);">
                    <span style="font-weight:800;font-size:1rem;">GROSS TOTAL</span>
                    <span style="font-weight:800;font-size:1rem;color:var(--teal);font-family:monospace;">{{ $projectInvoice->gross_total_formatted }}</span>
                </div>
                @if($projectInvoice->pph_percentage > 0)
                <div style="display:flex;justify-content:space-between;padding:10px 16px;border-bottom:1px solid rgba(255,255,255,0.04);">
                    <span style="color:#ef4444;">PPh ({{ $projectInvoice->pph_percentage }}%)</span>
                    <span style="color:#ef4444;font-family:monospace;">- {{ $projectInvoice->pph_amount_formatted }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:12px 16px;background:rgba(20,184,166,0.06);">
                    <span style="font-weight:700;">NET AMOUNT (diterima)</span>
                    <span style="font-weight:700;color:var(--teal);font-family:monospace;">{{ $projectInvoice->net_amount_formatted }}</span>
                </div>
                @endif
                <div style="display:flex;justify-content:space-between;padding:10px 16px;border-top:1px solid var(--glass-border);">
                    <span style="color:#22c55e;">Terbayar</span>
                    <span style="color:#22c55e;font-family:monospace;">{{ $projectInvoice->amount_paid_formatted }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 16px;{{ $projectInvoice->remaining_balance > 0 ? 'color:#ef4444;' : 'color:#22c55e;' }}">
                    <span style="font-weight:700;">Sisa Tagihan</span>
                    <span style="font-weight:700;font-family:monospace;">{{ $projectInvoice->remaining_balance_formatted }}</span>
                </div>
            </div>
        </div>

        {{-- Bank Info --}}
        @if($projectInvoice->bank_name || $projectInvoice->bank_account)
        <div class="admin-card">
            <h3 style="font-weight:700;margin-bottom:12px;">Informasi Pembayaran</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Bank</div>
                    <div style="font-weight:700;">{{ $projectInvoice->bank_name ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">No. Rekening</div>
                    <code style="font-size:0.9rem;">{{ $projectInvoice->bank_account ?? '-' }}</code>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Atas Nama</div>
                    <div>{{ $projectInvoice->bank_holder ?? '-' }}</div>
                </div>
            </div>
        </div>
        @endif

        @if($projectInvoice->notes)
        <div class="admin-card">
            <h3 style="font-weight:700;margin-bottom:8px;">Catatan</h3>
            <p style="font-size:0.9rem;color:var(--text-secondary);">{{ $projectInvoice->notes }}</p>
        </div>
        @endif
    </div>

    {{-- RIGHT: Action panels --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Payment Tracking --}}
        <div class="admin-card">
            <h3 style="font-weight:700;margin-bottom:16px;">Rekam Pembayaran</h3>
            <div style="margin-bottom:16px;padding:14px;background:rgba(255,255,255,0.04);border-radius:8px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                    <span style="font-size:0.85rem;color:var(--text-muted);">Gross Total</span>
                    <strong style="font-family:monospace;">{{ $projectInvoice->gross_total_formatted }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                    <span style="font-size:0.85rem;color:#22c55e;">Terbayar</span>
                    <strong style="font-family:monospace;color:#22c55e;">{{ $projectInvoice->amount_paid_formatted }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding-top:8px;border-top:1px solid var(--glass-border);">
                    <span style="font-size:0.85rem;color:#ef4444;">Sisa Tagihan</span>
                    <strong style="font-family:monospace;color:#ef4444;">{{ $projectInvoice->remaining_balance_formatted }}</strong>
                </div>
            </div>
            @if($projectInvoice->payment_status !== 'cancelled' && $projectInvoice->payment_status !== 'paid')
            <form method="POST" action="{{ route('admin.project-invoices.payment', $projectInvoice) }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Total Terbayar (Kumulatif)</label>
                    <input type="number" name="amount_paid" class="form-control" value="{{ $projectInvoice->amount_paid }}" min="0" step="any" max="{{ $projectInvoice->gross_total }}" required>
                    <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;">Isi total yang sudah diterima (bukan jumlah yang baru diterima)</div>
                </div>
                <button type="submit" class="admin-btn admin-btn--primary" style="width:100%;justify-content:center;">
                    💰 Update Pembayaran
                </button>
            </form>
            @elseif($projectInvoice->payment_status === 'paid')
            <div class="admin-alert admin-alert--success">✓ Invoice sudah lunas.</div>
            @endif
        </div>

        {{-- Status Update --}}
        <div class="admin-card">
            <h3 style="font-weight:700;margin-bottom:16px;">Update Status Manual</h3>
            <form method="POST" action="{{ route('admin.project-invoices.status', $projectInvoice) }}">
                @csrf
                <div class="form-group">
                    <select name="payment_status" class="form-control">
                        @foreach(['unpaid'=>'Unpaid','partial'=>'Partial','paid'=>'Paid','overdue'=>'Overdue','cancelled'=>'Cancelled'] as $v=>$l)
                            <option value="{{ $v }}" {{ $projectInvoice->payment_status === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="admin-btn admin-btn--warning" style="width:100%;justify-content:center;">Update Status</button>
            </form>
        </div>

        {{-- Quick Stats --}}
        <div class="admin-card">
            <h3 style="font-weight:700;margin-bottom:12px;">Persentase Terbayar</h3>
            @php
                $paidPct = $projectInvoice->gross_total > 0
                    ? min(100, round(($projectInvoice->amount_paid / $projectInvoice->gross_total) * 100, 1))
                    : 0;
            @endphp
            <div style="background:rgba(255,255,255,0.06);border-radius:8px;height:12px;margin-bottom:8px;overflow:hidden;">
                <div style="background:var(--teal);height:100%;width:{{ $paidPct }}%;transition:width 0.5s;"></div>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.85rem;">
                <span style="color:var(--text-muted);">Terbayar</span>
                <strong style="color:var(--teal);">{{ $paidPct }}%</strong>
            </div>
        </div>

        {{-- Delete --}}
        <form method="POST" action="{{ route('admin.project-invoices.destroy', $projectInvoice) }}"
              onsubmit="return confirm('Hapus Invoice ini? Tindakan tidak dapat dibatalkan.')">
            @csrf @method('DELETE')
            <button type="submit" class="admin-btn admin-btn--danger" style="width:100%;justify-content:center;">🗑 Hapus Invoice</button>
        </form>

        <div style="font-size:0.75rem;color:var(--text-muted);text-align:center;padding:8px;">
            Dibuat: {{ $projectInvoice->created_at->format('d M Y H:i') }}<br>
            Diperbarui: {{ $projectInvoice->updated_at->format('d M Y H:i') }}
        </div>
    </div>
</div>
{{-- Invoice Email Modal --}}
<div id="invoiceEmailModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.75);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
    <div style="background:#0f172a;border:1px solid var(--glass-border);border-radius:12px;padding:24px;width:100%;max-width:480px;box-shadow:0 20px 25px -5px rgba(0,0,0,0.5);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-size:1.1rem;font-weight:700;color:var(--text-primary);">✉️ Kirim Invoice via Email</h3>
            <button type="button" onclick="closeInvoiceEmailModal()" style="background:none;border:none;color:var(--text-muted);font-size:1.2rem;cursor:pointer;">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.project-invoices.send-email', $projectInvoice) }}">
            @csrf
            <div style="margin-bottom:14px;padding:8px 12px;background:rgba(255,255,255,0.04);border-radius:6px;font-size:0.82rem;">
                Status Saat Ini: <span class="badge {{ $ps['class'] }}">{{ $ps['label'] }}</span>
                @if($projectInvoice->payment_status === 'paid')
                    <span style="color:#22c55e;margin-left:6px;">(Bukti Lunas)</span>
                @elseif($projectInvoice->payment_status === 'partial')
                    <span style="color:#f59e0b;margin-left:6px;">(Update Sisa Tagihan)</span>
                @else
                    <span style="color:#38bdf8;margin-left:6px;">(Tagihan Baru)</span>
                @endif
            </div>
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="display:block;margin-bottom:6px;">Email Penerima (To) <span style="color:#ef4444;">*</span></label>
                <input type="email" name="recipient_email" class="form-control" value="{{ old('recipient_email', $projectInvoice->client_email) }}" placeholder="finance@perusahaan.com" required>
            </div>

            {{-- CC Email Tag Input --}}
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="display:block;margin-bottom:6px;">CC (Carbon Copy) <span style="color:var(--text-muted);font-weight:400;font-size:0.78rem;">(opsional)</span></label>
                <input type="hidden" name="cc_emails" id="invCcHidden">
                <div id="invCcBox" style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;min-height:42px;padding:6px 10px;background:var(--input-bg,rgba(255,255,255,0.05));border:1px solid var(--glass-border);border-radius:var(--radius-sm,6px);cursor:text;transition:border-color 0.2s;" onclick="document.getElementById('invCcInput').focus()">
                    <input id="invCcInput" type="text" placeholder="Ketik email lalu Enter..." autocomplete="off"
                        style="border:none;background:transparent;outline:none;color:var(--text-primary);font-size:0.875rem;min-width:200px;flex:1;padding:2px 0;">
                </div>
                <p style="margin-top:4px;font-size:0.75rem;color:var(--text-muted);">Tekan <kbd style="padding:1px 5px;border-radius:3px;border:1px solid var(--glass-border);font-size:0.7rem;">Enter</kbd> atau <kbd style="padding:1px 5px;border-radius:3px;border:1px solid var(--glass-border);font-size:0.7rem;">,</kbd> untuk menambah email. Klik ✕ untuk hapus.</p>
            </div>

            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label" style="display:block;margin-bottom:6px;">Pesan Tambahan (Opsional)</label>
                <textarea name="email_message" class="form-control" rows="3" placeholder="Tuliskan catatan khusus untuk finance klien..."></textarea>
            </div>
            <div style="padding:10px 14px;background:rgba(20,184,166,0.08);border-left:3px solid var(--teal);border-radius:4px;font-size:0.8rem;color:var(--text-muted);margin-bottom:18px;">
                📎 File PDF Invoice resmi (<code>Invoice-{{ str_replace(['/','\\'],'-',$projectInvoice->invoice_number) }}.pdf</code>) akan otomatis dilampirkan.
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;">
                <button type="button" onclick="closeInvoiceEmailModal()" class="admin-btn admin-btn--secondary">Batal</button>
                <button type="submit" class="admin-btn admin-btn--primary">Kirim Invoice Sekarang</button>
            </div>
        </form>
    </div>
</div>

<script>
function openInvoiceEmailModal() {
    document.getElementById('invoiceEmailModal').style.display = 'flex';
}
function closeInvoiceEmailModal() {
    document.getElementById('invoiceEmailModal').style.display = 'none';
}

// ── CC Tag Input for Invoice Modal ──────────────────────────────────────────
(function() {
    var ccTags   = [];
    var box      = document.getElementById('invCcBox');
    var input    = document.getElementById('invCcInput');
    var hidden   = document.getElementById('invCcHidden');

    function renderTags() {
        // Remove old chips
        box.querySelectorAll('.cc-chip').forEach(function(el) { el.remove(); });
        ccTags.forEach(function(email, idx) {
            var chip = document.createElement('span');
            chip.className = 'cc-chip';
            chip.style.cssText = 'display:inline-flex;align-items:center;gap:4px;padding:3px 8px;background:rgba(20,184,166,0.18);border:1px solid rgba(20,184,166,0.4);border-radius:20px;font-size:0.78rem;color:var(--teal,#14b8a6);white-space:nowrap;';
            chip.innerHTML = '<span>' + email + '</span>';
            var removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.textContent = '\u00d7';
            removeBtn.style.cssText = 'background:none;border:none;cursor:pointer;color:inherit;padding:0;line-height:1;font-size:0.9rem;';
            removeBtn.addEventListener('click', function() { removeTag(idx); });
            chip.appendChild(removeBtn);
            box.insertBefore(chip, input);
        });
        hidden.value = ccTags.join(',');
    }

    function addTag(raw) {
        var emails = raw.split(/[,;]+/).map(function(e) { return e.trim().toLowerCase(); }).filter(Boolean);
        emails.forEach(function(email) {
            if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) && ccTags.indexOf(email) === -1) {
                ccTags.push(email);
            }
        });
        renderTags();
        input.value = '';
    }

    function removeTag(idx) {
        ccTags.splice(idx, 1);
        renderTags();
    }

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ',' || e.key === ';') {
            e.preventDefault();
            if (input.value.trim()) addTag(input.value);
        } else if (e.key === 'Backspace' && input.value === '' && ccTags.length > 0) {
            ccTags.pop();
            renderTags();
        }
    });
    input.addEventListener('blur', function() {
        if (input.value.trim()) addTag(input.value);
    });
    input.addEventListener('paste', function(e) {
        e.preventDefault();
        var pasted = (e.clipboardData || window.clipboardData).getData('text');
        addTag(pasted);
    });
})();
</script>

@endsection
