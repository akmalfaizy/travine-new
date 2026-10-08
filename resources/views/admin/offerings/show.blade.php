@extends('admin.layout')
@section('title', 'Detail Offering — ' . $offering->offering_number)
@section('content')

@php $badge = $offering->status_badge; @endphp

<div class="admin-topbar">
    <div>
        <h1>{{ $offering->offering_number }}</h1>
        <p style="color:var(--text-muted);font-size:0.85rem;margin-top:4px;">
            {{ $offering->package_name }} · {{ $offering->client_name }}
        </p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <button type="button" onclick="openEmailModal()" class="admin-btn admin-btn--primary">
            ✉️ Kirim via Email
        </button>
        <a href="{{ route('admin.offerings.proposal-pdf', $offering) }}" target="_blank" class="admin-btn admin-btn--secondary">
            📄 Download Proposal PDF
        </a>
        @if(in_array($offering->status, ['draft','sent','revised']))
            <a href="{{ route('admin.offerings.edit', $offering) }}" class="admin-btn admin-btn--warning">✏️ Edit</a>
        @endif
        @if($offering->status === 'approved')
            <a href="{{ route('admin.offerings.create-invoice', $offering) }}" class="admin-btn admin-btn--primary" style="background:#10b981;">
                🧾 Buat Invoice
            </a>
        @endif
        <a href="{{ route('admin.offerings.index') }}" class="admin-btn admin-btn--secondary">← Kembali</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;">

    {{-- LEFT: Document body --}}
    <div style="display:flex;flex-direction:column;gap:20px;">

        {{-- Header Info --}}
        <div class="admin-card">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;">
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">No. Offering</div>
                    <code style="font-size:1rem;font-weight:800;color:var(--teal);">{{ $offering->offering_number }}</code>
                </div>
                <span class="badge {{ $badge['class'] }}" style="font-size:0.85rem;padding:6px 14px;">{{ $badge['label'] }}</span>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Client</div>
                    <div style="font-weight:700;">{{ $offering->client_name }}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Provider</div>
                    <div>{{ $offering->provider ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">PIC Client</div>
                    <div>{{ $offering->pic_client ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">PIC Project</div>
                    <div>{{ $offering->pic_project ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">No. Kontak PIC</div>
                    <div>{{ $offering->pic_contact ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Email Klien</div>
                    <div style="color:var(--teal);font-weight:600;">{{ $offering->client_email ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Tanggal & Durasi</div>
                    <div>{{ $offering->date_duration ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Nama Paket</div>
                    <div style="font-weight:700;color:var(--text-primary);">{{ $offering->package_name }}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Jumlah Peserta</div>
                    <div style="font-weight:700;">{{ number_format($offering->participants) }} orang</div>
                </div>
            </div>
            @if($offering->description)
            <div style="margin-top:16px;padding:12px;background:rgba(255,255,255,0.04);border-radius:8px;">
                <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:6px;">Deskripsi</div>
                <div style="font-size:0.9rem;">{{ $offering->description }}</div>
            </div>
            @endif
        </div>

        {{-- Budget Table --}}
        <div class="admin-card">
            <h3 style="font-weight:700;margin-bottom:16px;">Detail Item Budget</h3>
            @forelse($offering->categories as $cat)
                <div style="margin-bottom:20px;">
                    <div style="background:rgba(20,184,166,0.08);border-left:3px solid var(--teal);padding:8px 14px;font-weight:700;margin-bottom:0;border-radius:4px 4px 0 0;">
                        {{ $cat->name }}
                    </div>
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
                                    <th style="text-align:right;">Total Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($cat->items as $item)
                                <tr>
                                    <td><strong>{{ $item->component ?: '-' }}</strong></td>
                                    <td style="font-size:0.85rem;color:var(--text-muted);">{{ $item->description ?: '-' }}</td>
                                    <td>
                                        @if(!empty($item->remark))
                                            <span class="badge {{ str_contains(strtolower($item->remark ?? ''), 'optional') ? 'badge-warning' : 'badge-info' }}" style="font-size:0.72rem;">
                                                {{ $item->remark }}
                                            </span>
                                        @else
                                            <span style="color:var(--text-muted);">-</span>
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        @if(!empty($item->type))
                                            <span class="badge {{ $item->type === 'V' ? 'badge-info' : 'badge-warning' }}">{{ $item->type }}</span>
                                        @else
                                            <span style="color:var(--text-muted);">-</span>
                                        @endif
                                    </td>
                                    <td style="text-align:right;">{{ ($item->quantity !== null && $item->quantity !== '') ? number_format($item->quantity, 0, ',', '.') : '-' }}</td>
                                    <td style="text-align:right;font-family:monospace;">{{ ($item->unit_price !== null && $item->unit_price !== '') ? $item->unit_price_formatted : '-' }}</td>
                                    <td style="text-align:right;font-weight:700;font-family:monospace;color:var(--teal);">{{ ($item->total_cost !== null && $item->total_cost !== '') ? $item->total_cost_formatted : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align:center;color:var(--text-muted);padding:14px;">Belum ada item dalam kategori ini.</td>
                                </tr>
                            @endforelse
                            </tbody>
                            <tfoot>
                                <tr style="background:rgba(20,184,166,0.05);">
                                    <td colspan="6" style="text-align:right;font-size:0.8rem;color:var(--text-muted);">Subtotal {{ $cat->name }}</td>
                                    <td style="text-align:right;font-weight:700;font-family:monospace;">{{ $cat->total_cost_formatted }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @empty
                <p style="color:var(--text-muted);">Belum ada item budget.</p>
            @endforelse

            {{-- Cost Summary --}}
            <div style="max-width:420px;margin-left:auto;margin-top:24px;border:1px solid rgba(20,184,166,0.25);border-radius:var(--radius-sm);overflow:hidden;">
                <div style="background:rgba(20,184,166,0.08);padding:10px 16px;font-weight:700;font-size:0.85rem;text-transform:uppercase;letter-spacing:0.05em;color:var(--teal);">Ringkasan Biaya</div>
                @php
                    $rows = [
                        ['label' => 'Biaya Produksi', 'val' => $offering->production_cost_formatted, 'bold' => false],
                        ['label' => 'Biaya Lain-Lain (' . $offering->other_cost_percentage . '%)', 'val' => $offering->other_cost_formatted, 'bold' => false],
                        ['label' => 'Management Fee (' . $offering->management_fee_percentage . '%)', 'val' => $offering->management_fee_formatted, 'bold' => false],
                    ];
                @endphp
                @foreach($rows as $r)
                <div style="display:flex;justify-content:space-between;padding:10px 16px;border-bottom:1px solid rgba(255,255,255,0.04);{{ $r['bold'] ? 'font-weight:700;' : '' }}">
                    <span style="color:var(--text-secondary);">{{ $r['label'] }}</span>
                    <span style="font-family:monospace;">{{ $r['val'] }}</span>
                </div>
                @endforeach
                <div style="display:flex;justify-content:space-between;padding:14px 16px;background:rgba(20,184,166,0.12);border-top:2px solid var(--teal);">
                    <span style="font-weight:800;font-size:1rem;">GRAND TOTAL</span>
                    <span style="font-weight:800;font-size:1rem;color:var(--teal);font-family:monospace;">{{ $offering->total_amount_formatted }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 16px;background:rgba(20,184,166,0.06);">
                    <span style="color:var(--text-secondary);">Price Per Pack ({{ number_format($offering->participants) }} pax)</span>
                    <span style="font-weight:700;color:var(--teal);font-family:monospace;">{{ $offering->price_per_pack_formatted }}</span>
                </div>
            </div>

            {{-- Legend --}}
            <div style="margin-top:16px;padding:10px 14px;background:rgba(255,255,255,0.03);border-radius:8px;font-size:0.8rem;color:var(--text-muted);">
                <strong style="color:var(--text-secondary);">Keterangan Type:</strong> V = Variable Cost &nbsp;|&nbsp; F = Fixed Cost
            </div>
        </div>

        {{-- Notes --}}
        @if(!empty($offering->notes) && count($offering->notes))
        <div class="admin-card">
            <h3 style="font-weight:700;margin-bottom:12px;">Keterangan</h3>
            <ul style="margin:0;padding-left:20px;display:flex;flex-direction:column;gap:6px;">
                @foreach($offering->notes as $note)
                    @if(!empty(trim($note)))
                    <li style="font-size:0.9rem;">{{ $note }}</li>
                    @endif
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Revision Notes --}}
        @if(!empty($offering->revision_notes) && count($offering->revision_notes))
        <div class="admin-card">
            <h3 style="font-weight:700;margin-bottom:12px;">Catatan Revisi</h3>
            @foreach($offering->revision_notes as $i => $rev)
                @if(!empty(trim($rev['text'] ?? '')))
                <div style="background:rgba(251,191,36,0.06);border-left:3px solid #fbbf24;padding:12px 14px;border-radius:0 6px 6px 0;margin-bottom:10px;">
                    <div style="font-size:0.75rem;color:#fbbf24;font-weight:700;margin-bottom:4px;">Revisi #{{ $i + 1 }}</div>
                    <div style="font-size:0.9rem;">{{ $rev['text'] }}</div>
                </div>
                @endif
            @endforeach
        </div>
        @endif

        {{-- Recommendation --}}
        @if($offering->recommendation)
        <div class="admin-card" style="border-left:3px solid var(--teal);">
            <h3 style="font-weight:700;margin-bottom:12px;">Rekomendasi</h3>
            <p style="font-size:0.9rem;line-height:1.6;">{{ $offering->recommendation }}</p>
        </div>
        @endif

        {{-- Linked Invoices --}}
        @if($offering->invoices->count())
        <div class="admin-card">
            <h3 style="font-weight:700;margin-bottom:12px;">Invoice Terkait ({{ $offering->invoices->count() }})</h3>
            <table class="admin-table">
                <thead><tr><th>No. Invoice</th><th>Tgl Terbit</th><th>Jatuh Tempo</th><th>Grand Total</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                @foreach($offering->invoices as $inv)
                    @php $ps = $inv->payment_status_badge; @endphp
                    <tr>
                        <td><code style="color:var(--teal);">{{ $inv->invoice_number }}</code></td>
                        <td>{{ $inv->issue_date->format('d M Y') }}</td>
                        <td>{{ $inv->due_date->format('d M Y') }}</td>
                        <td><strong>{{ $inv->gross_total_formatted }}</strong></td>
                        <td><span class="badge {{ $ps['class'] }}">{{ $ps['label'] }}</span></td>
                        <td><a href="{{ route('admin.project-invoices.show', $inv) }}" class="admin-btn admin-btn--secondary admin-btn--sm">Lihat</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- RIGHT: Actions panel --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Status Transition --}}
        <div class="admin-card">
            <h3 style="font-weight:700;margin-bottom:16px;">Update Status</h3>
            @php
                $allowedTransitions = [
                    'draft'   => ['sent' => 'Kirim ke Klien'],
                    'sent'    => ['revised' => 'Tandai Direvisi', 'approved' => 'Approve ✓', 'rejected' => 'Reject ✗', 'expired' => 'Expired'],
                    'revised' => ['sent' => 'Kirim Ulang', 'approved' => 'Approve ✓', 'rejected' => 'Reject ✗'],
                ];
                $transitions = $allowedTransitions[$offering->status] ?? [];
            @endphp
            @if(count($transitions))
                @foreach($transitions as $statusVal => $statusLabel)
                <form method="POST" action="{{ route('admin.offerings.status', $offering) }}" style="margin-bottom:8px;"
                      onsubmit="return confirm('{{ in_array($statusVal, ['approved','rejected']) ? 'Yakin '.$statusLabel.'?' : '' }}')">
                    @csrf
                    <input type="hidden" name="status" value="{{ $statusVal }}">
                    <button type="submit" class="admin-btn admin-btn--{{ in_array($statusVal, ['approved']) ? 'primary' : (in_array($statusVal, ['rejected']) ? 'danger' : 'secondary') }}" style="width:100%;justify-content:center;">
                        {{ $statusLabel }}
                    </button>
                </form>
                @endforeach
            @else
                <p style="color:var(--text-muted);font-size:0.85rem;">Status <strong>{{ $offering->status }}</strong> sudah final.</p>
            @endif
        </div>

        {{-- Quick Stats --}}
        <div class="admin-card">
            <h3 style="font-weight:700;margin-bottom:16px;">Ringkasan Cepat</h3>
            <div style="display:flex;flex-direction:column;gap:12px;">
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:var(--text-muted);font-size:0.85rem;">Total Item</span>
                    <strong>{{ $offering->categories->sum(fn($c) => $c->items->count()) }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:var(--text-muted);font-size:0.85rem;">Kategori</span>
                    <strong>{{ $offering->categories->count() }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:var(--text-muted);font-size:0.85rem;">Peserta</span>
                    <strong>{{ number_format($offering->participants) }}</strong>
                </div>
                <hr style="border-color:var(--glass-border);">
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:var(--text-muted);font-size:0.85rem;">Biaya Produksi</span>
                    <strong style="font-family:monospace;">{{ $offering->production_cost_formatted }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:var(--text-muted);font-size:0.85rem;">Grand Total</span>
                    <strong style="color:var(--teal);font-family:monospace;">{{ $offering->total_amount_formatted }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:var(--text-muted);font-size:0.85rem;">Price/Pax</span>
                    <strong style="color:var(--teal);font-family:monospace;">{{ $offering->price_per_pack_formatted }}</strong>
                </div>
            </div>
        </div>

        {{-- Danger Zone --}}
        @if(!$offering->invoices->count())
        <form method="POST" action="{{ route('admin.offerings.destroy', $offering) }}"
              onsubmit="return confirm('Hapus Offering ini? Tindakan tidak dapat dibatalkan.')">
            @csrf @method('DELETE')
            <button type="submit" class="admin-btn admin-btn--danger" style="width:100%;justify-content:center;">
                🗑 Hapus Offering
            </button>
        </form>
        @endif

        {{-- Timestamps --}}
        <div style="font-size:0.75rem;color:var(--text-muted);text-align:center;padding:8px;">
            Dibuat: {{ $offering->created_at->format('d M Y H:i') }}<br>
            Diperbarui: {{ $offering->updated_at->format('d M Y H:i') }}
        </div>
    </div>
</div>

{{-- Email Modal --}}
<div id="emailModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.75);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
    <div style="background:#0f172a;border:1px solid var(--glass-border);border-radius:12px;padding:24px;width:100%;max-width:480px;box-shadow:0 20px 25px -5px rgba(0,0,0,0.5);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-size:1.1rem;font-weight:700;color:var(--text-primary);">✉️ Kirim Proposal via Email</h3>
            <button type="button" onclick="closeEmailModal()" style="background:none;border:none;color:var(--text-muted);font-size:1.2rem;cursor:pointer;">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.offerings.send-email', $offering) }}">
            @csrf
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="display:block;margin-bottom:6px;">Email Penerima (To) <span style="color:#ef4444;">*</span></label>
                <input type="email" name="recipient_email" class="form-control" value="{{ old('recipient_email', $offering->client_email) }}" placeholder="nama@perusahaan.com" required>
            </div>

            {{-- CC Email Tag Input --}}
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label" style="display:block;margin-bottom:6px;">CC (Carbon Copy) <span style="color:var(--text-muted);font-weight:400;font-size:0.78rem;">(opsional)</span></label>
                <input type="hidden" name="cc_emails" id="offerCcHidden">
                <div id="offerCcBox" style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;min-height:42px;padding:6px 10px;background:var(--input-bg,rgba(255,255,255,0.05));border:1px solid var(--glass-border);border-radius:var(--radius-sm,6px);cursor:text;transition:border-color 0.2s;" onclick="document.getElementById('offerCcInput').focus()">
                    <input id="offerCcInput" type="text" placeholder="Ketik email lalu Enter..." autocomplete="off"
                        style="border:none;background:transparent;outline:none;color:var(--text-primary);font-size:0.875rem;min-width:200px;flex:1;padding:2px 0;">
                </div>
                <p style="margin-top:4px;font-size:0.75rem;color:var(--text-muted);">Tekan <kbd style="padding:1px 5px;border-radius:3px;border:1px solid var(--glass-border);font-size:0.7rem;">Enter</kbd> atau <kbd style="padding:1px 5px;border-radius:3px;border:1px solid var(--glass-border);font-size:0.7rem;">,</kbd> untuk menambah email. Klik ✕ untuk hapus.</p>
            </div>

            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label" style="display:block;margin-bottom:6px;">Pesan Khusus (Opsional)</label>
                <textarea name="email_message" class="form-control" rows="3" placeholder="Tuliskan catatan tambahan untuk klien..."></textarea>
            </div>
            <div style="padding:10px 14px;background:rgba(20,184,166,0.08);border-left:3px solid var(--teal);border-radius:4px;font-size:0.8rem;color:var(--text-muted);margin-bottom:18px;">
                📎 File PDF Proposal Penawaran (<code>Offering-{{ str_replace(['/','\\'],'-',$offering->offering_number) }}.pdf</code>) akan otomatis dilampirkan.<br>
                Status penawaran akan otomatis menjadi <strong>Sent</strong> jika saat ini Draft.
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;">
                <button type="button" onclick="closeEmailModal()" class="admin-btn admin-btn--secondary">Batal</button>
                <button type="submit" class="admin-btn admin-btn--primary">Kirim Proposal Sekarang</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEmailModal() {
    document.getElementById('emailModal').style.display = 'flex';
}
function closeEmailModal() {
    document.getElementById('emailModal').style.display = 'none';
}

// ── CC Tag Input for Offering Modal ───────────────────────────────────────
(function() {
    var ccTags = [];
    var box    = document.getElementById('offerCcBox');
    var input  = document.getElementById('offerCcInput');
    var hidden = document.getElementById('offerCcHidden');

    function renderTags() {
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
