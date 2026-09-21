@extends('admin.layout')
@section('title', 'Buat Offering Baru — Admin')

@push('styles')
<style>
/* ─── Wizard tabs ─────────────────────────────────────────────── */
.wizard-tabs { display:flex; gap:0; margin-bottom:24px; border-radius:var(--radius-sm); overflow:hidden; border:1px solid var(--glass-border); }
.wizard-tab  { flex:1; padding:12px 8px; text-align:center; font-size:0.78rem; font-weight:600; color:var(--text-muted); background:var(--glass-bg); cursor:pointer; transition:var(--transition); border-right:1px solid var(--glass-border); }
.wizard-tab:last-child { border-right:none; }
.wizard-tab.active { background:var(--teal); color:#fff; }
.wizard-tab.done   { background:rgba(20,184,166,0.15); color:var(--teal); }
.wizard-step { display:none; }
.wizard-step.active { display:block; }

/* ─── Category blocks ─────────────────────────────────────────── */
.cat-block { border:1px solid var(--glass-border); border-radius:var(--radius-sm); margin-bottom:16px; overflow:hidden; }
.cat-header { display:flex; align-items:center; gap:10px; padding:12px 16px; background:rgba(255,255,255,0.04); }
.cat-header input { flex:1; background:transparent; border:none; outline:none; color:var(--text-primary); font-weight:700; font-size:0.95rem; }
.cat-header input::placeholder { color:var(--text-muted); font-weight:400; }
.cat-items { padding:12px 16px 16px; }

/* ─── Item row ────────────────────────────────────────────────── */
.item-row { display:grid; grid-template-columns:2fr 1.5fr 1fr 80px 120px 140px 110px; gap:8px; align-items:start; padding:10px 0; border-bottom:1px solid rgba(255,255,255,0.04); }
.item-row:last-child { border-bottom:none; }
.item-row-labels { display:grid; grid-template-columns:2fr 1.5fr 1fr 80px 120px 140px 110px; gap:8px; padding:4px 0 8px; border-bottom:1px solid var(--glass-border); margin-bottom:8px; }
.item-row-labels span { font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--text-muted); }

/* ─── Calculated summary ──────────────────────────────────────── */
.calc-summary { background:rgba(20,184,166,0.06); border:1px solid rgba(20,184,166,0.2); border-radius:var(--radius-sm); padding:20px 24px; }
.calc-row     { display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.04); font-size:0.9rem; }
.calc-row:last-child { border-bottom:none; }
.calc-row.grand { border-top:2px solid var(--teal); margin-top:8px; padding-top:12px; font-size:1.1rem; font-weight:800; color:var(--text-primary); }
.calc-val { font-weight:700; font-family:'Courier New', monospace; }
.calc-val.teal { color:var(--teal); }

/* ─── Notes / revision ────────────────────────────────────────── */
.note-row { display:flex; gap:8px; margin-bottom:8px; align-items:center; }
.note-row input { flex:1; }
.rev-row  { background:rgba(255,255,255,0.03); border:1px solid var(--glass-border); border-radius:var(--radius-sm); padding:12px; margin-bottom:10px; }

/* ─── Master item picker ──────────────────────────────────────── */
.master-picker { position:relative; }
.master-list   { position:absolute; top:100%; left:0; right:0; background:#0f1a2e; border:1px solid var(--glass-border); border-radius:var(--radius-sm); max-height:260px; overflow-y:auto; z-index:200; display:none; }
.master-list.open { display:block; }
.master-item-opt { padding:10px 14px; cursor:pointer; font-size:0.88rem; border-bottom:1px solid rgba(255,255,255,0.04); }
.master-item-opt:hover { background:rgba(20,184,166,0.1); color:var(--teal); }
.master-item-opt small { display:block; color:var(--text-muted); font-size:0.75rem; margin-top:2px; }

@media(max-width:900px){
    .item-row, .item-row-labels { grid-template-columns:1fr; }
    .item-row-labels { display:none; }
}
</style>
@endpush

@section('content')
<div class="admin-topbar">
    <div>
        <h1>Buat Offering Baru</h1>
        <p style="color:var(--text-muted);font-size:0.85rem;margin-top:4px;">Production Cost Proposal untuk klien</p>
    </div>
    <a href="{{ route('admin.offerings.index') }}" class="admin-btn admin-btn--secondary">← Kembali</a>
</div>

<form id="offeringForm" method="POST" action="{{ route('admin.offerings.store') }}">
@csrf

{{-- Validation Errors --}}
@if($errors->any())
<div style="background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.4);border-radius:10px;padding:16px 20px;margin-bottom:20px;">
    <div style="font-weight:700;color:#f87171;margin-bottom:8px;">⚠ Terdapat kesalahan pada form. Silakan periksa kembali:</div>
    <ul style="margin:0;padding-left:20px;color:#fca5a5;font-size:0.88rem;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif



{{-- Wizard Tabs --}}
<div class="wizard-tabs" id="wizardTabs">
    <div class="wizard-tab active" data-step="1">① Client & Project</div>
    <div class="wizard-tab" data-step="2">② Budget Items</div>
    <div class="wizard-tab" data-step="3">③ Biaya Tambahan</div>
    <div class="wizard-tab" data-step="4">④ Catatan & Rekomendasi</div>
    <div class="wizard-tab" data-step="5">⑤ Preview & Simpan</div>
</div>

{{-- ══ STEP 1: Client & Project ══ --}}
<div class="wizard-step active" id="step1">
    <div class="admin-card">
        <h3 style="font-weight:700;margin-bottom:20px;">Informasi Klien & Proyek</h3>
        <div class="admin-grid admin-grid--2">
            <div class="form-group">
                <label class="form-label">Nama Klien / Perusahaan <span style="color:#ef4444;">*</span></label>
                <input type="text" name="client_name" class="form-control" value="{{ old('client_name') }}" placeholder="PT. Cimory - Bawen" required>
            </div>
            <div class="form-group">
                <label class="form-label">Provider / Penyelenggara</label>
                <input type="text" name="provider" class="form-control" value="{{ old('provider', 'CV. Nara Tour') }}" placeholder="CV. Nara Tour">
            </div>
            <div class="form-group">
                <label class="form-label">PIC Client</label>
                <input type="text" name="pic_client" class="form-control" value="{{ old('pic_client') }}" placeholder="Indirect Procurement">
            </div>
            <div class="form-group">
                <label class="form-label">PIC Project (Internal)</label>
                <input type="text" name="pic_project" class="form-control" value="{{ old('pic_project') }}" placeholder="Rezi Edi Muin">
            </div>
            <div class="form-group">
                <label class="form-label">No. Kontak PIC</label>
                <input type="text" name="pic_contact" class="form-control" value="{{ old('pic_contact') }}" placeholder="0812-xxxx-xxxx">
            </div>
            <div class="form-group">
                <label class="form-label">Email Klien / PIC</label>
                <input type="email" name="client_email" class="form-control" value="{{ old('client_email') }}" placeholder="procurement@perusahaan.com">
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;">Digunakan untuk pengiriman proposal via email</div>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Paket / Event <span style="color:#ef4444;">*</span></label>
                <input type="text" name="package_name" class="form-control" value="{{ old('package_name') }}" placeholder="Employee Gathering Yogyakarta 2026" required>
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal & Durasi</label>
                <input type="text" name="date_duration" class="form-control" value="{{ old('date_duration') }}" placeholder="24 Oktober 2026 - One Day Trip">
            </div>
            <div class="form-group">
                <label class="form-label">Jumlah Peserta <span style="color:#ef4444;">*</span></label>
                <input type="number" name="participants" id="participantsInput" class="form-control" value="{{ old('participants', 1) }}" min="1" required>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Deskripsi Singkat</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi kegiatan/event...">{{ old('description') }}</textarea>
        </div>
        <div style="display:flex;justify-content:flex-end;margin-top:8px;">
            <button type="button" class="admin-btn admin-btn--primary" onclick="goStep(2)">Lanjut → Budget Items</button>
        </div>
    </div>
</div>

{{-- ══ STEP 2: Budget Items ══ --}}
<div class="wizard-step" id="step2">
    <div class="admin-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-weight:700;">Detail Item Budget</h3>
            <div style="display:flex;gap:8px;">
                <div class="master-picker" id="masterPicker">
                    <button type="button" class="admin-btn admin-btn--secondary" onclick="toggleMasterPicker()" id="masterPickerBtn">
                        📋 Tambah dari Master Item
                    </button>
                    <div class="master-list" id="masterList">
                        <div style="padding:8px 14px;font-size:0.75rem;font-weight:700;color:var(--text-muted);border-bottom:1px solid var(--glass-border);">
                            Pilih category tujuan terlebih dahulu, lalu klik item:
                        </div>
                        @foreach($masterByCategory as $catName => $items)
                        <div style="padding:6px 14px;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--teal);background:rgba(20,184,166,0.06);">{{ $catName }}</div>
                        @foreach($items as $mi)
                        <div class="master-item-opt" onclick="addMasterItem('{{ addslashes($catName) }}', '{{ addslashes($mi->component) }}', '{{ addslashes($mi->description ?? '') }}', '{{ $mi->default_type }}', '{{ addslashes($mi->default_remark) }}', {{ $mi->default_price }})">
                            {{ $mi->component }}
                            <small>{{ $mi->default_remark }} · {{ $mi->default_type === 'V' ? 'Variable' : 'Fixed' }}</small>
                        </div>
                        @endforeach
                        @endforeach
                    </div>
                </div>
                <button type="button" class="admin-btn admin-btn--primary" onclick="addCategory()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Tambah Kategori
                </button>
            </div>
        </div>

        <div id="categoriesContainer">
            {{-- Dynamically inserted --}}
        </div>

        <div id="emptyState" style="text-align:center;padding:40px;color:var(--text-muted);border:2px dashed var(--glass-border);border-radius:var(--radius-sm);">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 12px;display:block;opacity:0.4;"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="12" y1="8" x2="12" y2="16"/></svg>
            <p>Belum ada kategori. Klik <strong>Tambah Kategori</strong> atau gunakan <strong>Master Item</strong>.</p>
        </div>

        <div style="display:flex;justify-content:space-between;margin-top:20px;">
            <button type="button" class="admin-btn admin-btn--secondary" onclick="goStep(1)">← Kembali</button>
            <button type="button" class="admin-btn admin-btn--primary" onclick="goStep(3)">Lanjut → Biaya Tambahan</button>
        </div>
    </div>
</div>

{{-- ══ STEP 3: Additional Cost ══ --}}
<div class="wizard-step" id="step3">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
        <div class="admin-card">
            <h3 style="font-weight:700;margin-bottom:20px;">Persentase Biaya</h3>
            <div class="form-group">
                <label class="form-label">Biaya Lain-Lain (%)</label>
                <input type="number" name="other_cost_percentage" id="otherCostPct" class="form-control" value="{{ old('other_cost_percentage', 5) }}" min="0" max="100" step="0.01" oninput="recalc()">
                <div style="font-size:0.78rem;color:var(--text-muted);margin-top:4px;">Default: 5% dari Biaya Produksi</div>
            </div>
            <div class="form-group">
                <label class="form-label">Management Fee (%)</label>
                <input type="number" name="management_fee_percentage" id="mgmtFeePct" class="form-control" value="{{ old('management_fee_percentage', 15) }}" min="0" max="100" step="0.01" oninput="recalc()">
                <div style="font-size:0.78rem;color:var(--text-muted);margin-top:4px;">Default: 15% dari Biaya Produksi</div>
            </div>
            <div style="padding:12px;background:rgba(255,255,255,0.04);border-radius:var(--radius-sm);font-size:0.82rem;color:var(--text-muted);">
                <strong style="color:var(--text-secondary);">Formula:</strong><br>
                Management Fee = Biaya Produksi × %<br>
                Biaya Lain-Lain = Biaya Produksi × %<br>
                Grand Total = Produksi + Lain + Fee
            </div>
        </div>

        <div class="calc-summary">
            <h3 style="font-weight:700;margin-bottom:16px;">Ringkasan Biaya</h3>
            <div class="calc-row">
                <span>Biaya Produksi</span>
                <span class="calc-val" id="dispProductionCost">Rp 0</span>
            </div>
            <div class="calc-row">
                <span>Biaya Lain-Lain (<span id="dispOtherPct">5</span>%)</span>
                <span class="calc-val" id="dispOtherCost">Rp 0</span>
            </div>
            <div class="calc-row">
                <span>Management Fee (<span id="dispMgmtPct">15</span>%)</span>
                <span class="calc-val" id="dispMgmtFee">Rp 0</span>
            </div>
            <div class="calc-row grand">
                <span>GRAND TOTAL</span>
                <span class="calc-val teal" id="dispGrandTotal">Rp 0</span>
            </div>
            <div class="calc-row" style="margin-top:8px;">
                <span>Price Per Pack (<span id="dispParticipants">1</span> pax)</span>
                <span class="calc-val teal" id="dispPricePerPack">Rp 0</span>
            </div>
        </div>
    </div>

    <div style="display:flex;justify-content:space-between;margin-top:20px;">
        <button type="button" class="admin-btn admin-btn--secondary" onclick="goStep(2)">← Kembali</button>
        <button type="button" class="admin-btn admin-btn--primary" onclick="goStep(4)">Lanjut → Catatan</button>
    </div>
</div>

{{-- ══ STEP 4: Notes / Revision / Recommendation ══ --}}
<div class="wizard-step" id="step4">
    <div class="admin-card">
        <h3 style="font-weight:700;margin-bottom:20px;">Keterangan, Revisi & Rekomendasi</h3>

        {{-- Notes --}}
        <div class="form-group">
            <label class="form-label" style="margin-bottom:10px;">Keterangan</label>
            <div id="notesContainer">
                <div class="note-row">
                    <input type="text" name="notes[]" class="form-control" placeholder="Contoh: Variable Cost (V)" value="{{ old('notes.0') }}">
                    <button type="button" onclick="removeNote(this)" class="admin-btn admin-btn--danger admin-btn--sm">✕</button>
                </div>
                <div class="note-row">
                    <input type="text" name="notes[]" class="form-control" placeholder="Contoh: Fixed Cost (F)" value="{{ old('notes.1') }}">
                    <button type="button" onclick="removeNote(this)" class="admin-btn admin-btn--danger admin-btn--sm">✕</button>
                </div>
            </div>
            <button type="button" onclick="addNote()" class="admin-btn admin-btn--secondary admin-btn--sm" style="margin-top:8px;">+ Tambah Keterangan</button>
        </div>

        {{-- Revision notes --}}
        <div class="form-group" style="margin-top:24px;">
            <label class="form-label" style="margin-bottom:10px;">Catatan Revisi</label>
            <div id="revisionContainer">
                @if(old('revision_notes'))
                    @foreach(old('revision_notes') as $i => $rn)
                    <div class="rev-row">
                        <div style="display:flex;gap:8px;align-items:flex-start;">
                            <textarea name="revision_notes[{{ $i }}][text]" class="form-control" rows="2" placeholder="Deskripsi catatan revisi...">{{ $rn['text'] ?? '' }}</textarea>
                            <button type="button" onclick="removeRevision(this)" class="admin-btn admin-btn--danger admin-btn--sm">✕</button>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
            <button type="button" onclick="addRevision()" class="admin-btn admin-btn--secondary admin-btn--sm" style="margin-top:8px;">+ Tambah Catatan Revisi</button>
        </div>

        {{-- Recommendation --}}
        <div class="form-group" style="margin-top:24px;">
            <label class="form-label">Rekomendasi (Opsional)</label>
            <textarea name="recommendation" class="form-control" rows="4" placeholder="Kami merekomendasikan...">{{ old('recommendation') }}</textarea>
            <div style="font-size:0.78rem;color:var(--text-muted);margin-top:4px;">Jika kosong, bagian ini tidak akan ditampilkan pada PDF.</div>
        </div>

        <div style="display:flex;justify-content:space-between;margin-top:20px;">
            <button type="button" class="admin-btn admin-btn--secondary" onclick="goStep(3)">← Kembali</button>
            <button type="button" class="admin-btn admin-btn--primary" onclick="goStep(5)">Lanjut → Preview</button>
        </div>
    </div>
</div>

{{-- ══ STEP 5: Preview & Save ══ --}}
<div class="wizard-step" id="step5">
    <div class="admin-card" id="previewContent">
        <div style="text-align:center;padding:32px;color:var(--text-muted);">
            <p>Preview akan muncul saat kamu membuka tab ini.</p>
        </div>
    </div>
    <div style="display:flex;justify-content:space-between;margin-top:20px;gap:12px;">
        <button type="button" class="admin-btn admin-btn--secondary" onclick="goStep(4)">← Kembali</button>
        <div style="display:flex;gap:10px;">
            <button type="submit" class="admin-btn admin-btn--primary" style="min-width:140px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Simpan sebagai Draft
            </button>
        </div>
    </div>
</div>

</form>

@push('scripts')
<script>
// ─── State ─────────────────────────────────────────────────────────────────
// Restore categories from old() input when validation fails (so items are not lost)
@php
    $oldCategories = [];
    if ($errors->any() && old('categories')) {
        foreach (old('categories') as $ci => $catOld) {
            $catEntry = ['id' => $ci + 1, 'name' => $catOld['name'] ?? '', 'items' => []];
            foreach ($catOld['items'] ?? [] as $ii => $itemOld) {
                $catEntry['items'][] = [
                    'id'          => $ii + 1,
                    'component'   => $itemOld['component'] ?? '',
                    'description' => $itemOld['description'] ?? '',
                    'remark'      => $itemOld['remark'] ?? '',
                    'type'        => $itemOld['type'] ?? 'V',
                    'qty'         => (float)($itemOld['quantity'] ?? 0),
                    'price'       => (float)($itemOld['unit_price'] ?? 0),
                ];
            }
            $oldCategories[] = $catEntry;
        }
    }
@endphp
let categories = {!! json_encode($oldCategories) !!};
let catCounter  = categories.length;
let itemCounter = categories.reduce((m, c) => Math.max(m, c.items.length), 0);
// Give each item a unique sequential id
(function() {
    let ic = 0;
    categories.forEach(c => c.items.forEach(i => { i.id = ++ic; }));
    itemCounter = ic;
})();

// ─── Wizard Navigation ─────────────────────────────────────────────────────
function goStep(n) {
    document.querySelectorAll('.wizard-step').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.wizard-tab').forEach(t => {
        const tn = parseInt(t.dataset.step);
        t.classList.remove('active','done');
        if (tn === n) t.classList.add('active');
        else if (tn < n) t.classList.add('done');
    });
    document.getElementById('step' + n).classList.add('active');
    if (n === 3) recalc();
    if (n === 5) renderPreview();
    window.scrollTo({top:0, behavior:'smooth'});
}
document.querySelectorAll('.wizard-tab').forEach(t => {
    t.addEventListener('click', () => goStep(parseInt(t.dataset.step)));
});

// ─── Init on page load ─────────────────────────────────────────────────────
(function() {
    // If there are restored categories (after validation error), render them
    if (categories.length > 0) {
        renderCategories();
        if (document.getElementById('emptyState'))
            document.getElementById('emptyState').style.display = 'none';
    }
    // If validation failed, jump to step 1 to show errors
    @if($errors->any())
    goStep(1);
    @endif
})();


// ─── Category & Item Management ────────────────────────────────────────────
function addCategory(name) {
    const id = ++catCounter;
    categories.push({ id, name: name || '', items: [] });
    renderCategories();
    document.getElementById('emptyState').style.display = 'none';
}

function removeCategory(id) {
    if (!confirm('Hapus kategori ini beserta semua itemnya?')) return;
    categories = categories.filter(c => c.id !== id);
    renderCategories();
    if (categories.length === 0) document.getElementById('emptyState').style.display = '';
}

function addItem(catId) {
    const cat = categories.find(c => c.id === catId);
    if (!cat) return;
    cat.items.push({ id: ++itemCounter, component:'', description:'', remark:'Included (Wajib)', type:'V', qty:1, price:0 });
    renderCategories();
}

function removeItem(catId, itemId) {
    const cat = categories.find(c => c.id === catId);
    if (!cat) return;
    cat.items = cat.items.filter(i => i.id !== itemId);
    renderCategories();
    recalc();
}

function updateCatName(catId, val) {
    const cat = categories.find(c => c.id === catId);
    if (cat) cat.name = val;
}

function updateItem(catId, itemId, field, val) {
    const cat  = categories.find(c => c.id === catId);
    if (!cat) return;
    const item = cat.items.find(i => i.id === itemId);
    if (!item) return;
    if (field === 'qty' || field === 'price') val = parseFloat(val) || 0;
    item[field] = val;
    recalc();
}

function renderCategories() {
    const container = document.getElementById('categoriesContainer');
    container.innerHTML = '';
    categories.forEach((cat, ci) => {
        const div = document.createElement('div');
        div.className = 'cat-block';
        div.innerHTML = `
        <div class="cat-header">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity:0.4;cursor:grab;flex-shrink:0;"><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/></svg>
            <input type="text" placeholder="Nama Kategori (mis: Transport)" value="${escHtml(cat.name)}" oninput="updateCatName(${cat.id}, this.value)">
            <button type="button" onclick="removeCategory(${cat.id})" class="admin-btn admin-btn--danger admin-btn--sm">✕ Hapus</button>
        </div>
        <div class="cat-items">
            ${cat.items.length ? `
            <div class="item-row-labels">
                <span>Component</span><span>Deskripsi</span><span>Remark</span>
                <span>Type</span><span>Qty</span><span>Harga Satuan</span><span>Total</span>
            </div>` : ''}
            ${cat.items.map(item => renderItemRow(cat.id, item)).join('')}
            <button type="button" onclick="addItem(${cat.id})" class="admin-btn admin-btn--secondary admin-btn--sm" style="margin-top:10px;">
                + Tambah Item
            </button>
        </div>`;

        // Hidden inputs for form submission
        div.innerHTML += `<input type="hidden" name="categories[${ci}][name]" value="${escHtml(cat.name)}">
                          <input type="hidden" name="categories[${ci}][sort_order]" value="${ci}">`;
        cat.items.forEach((item, ii) => {
            div.innerHTML += `
                <input type="hidden" name="categories[${ci}][items][${ii}][component]"   value="${escHtml(item.component)}">
                <input type="hidden" name="categories[${ci}][items][${ii}][description]" value="${escHtml(item.description)}">
                <input type="hidden" name="categories[${ci}][items][${ii}][remark]"      value="${escHtml(item.remark)}">
                <input type="hidden" name="categories[${ci}][items][${ii}][type]"        value="${escHtml(item.type)}">
                <input type="hidden" name="categories[${ci}][items][${ii}][quantity]"    value="${item.qty}">
                <input type="hidden" name="categories[${ci}][items][${ii}][unit_price]"  value="${item.price}">
                <input type="hidden" name="categories[${ci}][items][${ii}][sort_order]"  value="${ii}">`;
        });
        container.appendChild(div);
    });
    recalc();
}

function renderItemRow(catId, item) {
    const total = fmtRp(item.qty * item.price);
    return `<div class="item-row" id="itemrow-${item.id}">
        <input class="form-control" style="font-size:0.85rem;" placeholder="Component" value="${escHtml(item.component)}"
               oninput="updateItem(${catId},${item.id},'component',this.value)">
        <input class="form-control" style="font-size:0.85rem;" placeholder="Deskripsi singkat" value="${escHtml(item.description)}"
               oninput="updateItem(${catId},${item.id},'description',this.value)">
        <input class="form-control" style="font-size:0.85rem;" placeholder="Remark" value="${escHtml(item.remark)}"
               oninput="updateItem(${catId},${item.id},'remark',this.value)">
        <select class="form-control" style="font-size:0.85rem;" onchange="updateItem(${catId},${item.id},'type',this.value)">
            <option value="V" ${item.type==='V'?'selected':''}>V</option>
            <option value="F" ${item.type==='F'?'selected':''}>F</option>
        </select>
        <input type="number" class="form-control" style="font-size:0.85rem;" placeholder="Qty" value="${item.qty}" min="0" step="any"
               oninput="updateItem(${catId},${item.id},'qty',this.value)">
        <input type="number" class="form-control" style="font-size:0.85rem;" placeholder="Harga satuan" value="${item.price}" min="0" step="any"
               oninput="updateItem(${catId},${item.id},'price',this.value)">
        <div style="display:flex;align-items:center;gap:6px;">
            <span style="font-size:0.85rem;font-weight:700;color:var(--teal);font-family:monospace;" id="itemtotal-${item.id}">${total}</span>
            <button type="button" onclick="removeItem(${catId},${item.id})" class="admin-btn admin-btn--danger admin-btn--sm">✕</button>
        </div>
    </div>`;
}

// ─── Master Item Picker ─────────────────────────────────────────────────────
function toggleMasterPicker() {
    document.getElementById('masterList').classList.toggle('open');
}
document.addEventListener('click', e => {
    if (!document.getElementById('masterPicker').contains(e.target))
        document.getElementById('masterList').classList.remove('open');
});

function addMasterItem(catName, component, description, type, remark, price) {
    // Find or create matching category
    let cat = categories.find(c => c.name.toLowerCase() === catName.toLowerCase());
    if (!cat) {
        addCategory(catName);
        cat = categories[categories.length - 1];
    }
    cat.items.push({ id: ++itemCounter, component, description, remark, type, qty:1, price });
    renderCategories();
    document.getElementById('masterList').classList.remove('open');
    document.getElementById('emptyState').style.display = 'none';
}

// ─── Calculation Engine ────────────────────────────────────────────────────
function recalc() {
    let prodCost = 0;
    categories.forEach(cat => {
        cat.items.forEach(item => {
            prodCost += (item.qty || 0) * (item.price || 0);
        });
    });
    const participants = parseInt(document.getElementById('participantsInput')?.value) || 1;
    const otherPct = parseFloat(document.getElementById('otherCostPct')?.value) || 0;
    const mgmtPct  = parseFloat(document.getElementById('mgmtFeePct')?.value) || 0;

    const otherCost = prodCost * (otherPct / 100);
    const mgmtFee   = prodCost * (mgmtPct / 100);
    const grandTotal = prodCost + otherCost + mgmtFee;
    const perPack    = participants > 0 ? grandTotal / participants : 0;

    // Update item totals
    categories.forEach(cat => {
        cat.items.forEach(item => {
            const el = document.getElementById('itemtotal-' + item.id);
            if (el) el.textContent = fmtRp((item.qty||0)*(item.price||0));
        });
    });

    // Update summary
    const set = (id, val) => { const el = document.getElementById(id); if(el) el.textContent = val; };
    set('dispProductionCost', fmtRp(prodCost));
    set('dispOtherCost', fmtRp(otherCost));
    set('dispMgmtFee', fmtRp(mgmtFee));
    set('dispGrandTotal', fmtRp(grandTotal));
    set('dispPricePerPack', fmtRp(perPack));
    set('dispOtherPct', otherPct);
    set('dispMgmtPct', mgmtPct);
    set('dispParticipants', participants);
}

// ─── Notes ─────────────────────────────────────────────────────────────────
let noteCount = 2;
function addNote() {
    const row = document.createElement('div');
    row.className = 'note-row';
    row.innerHTML = `<input type="text" name="notes[]" class="form-control" placeholder="Keterangan tambahan...">
                     <button type="button" onclick="removeNote(this)" class="admin-btn admin-btn--danger admin-btn--sm">✕</button>`;
    document.getElementById('notesContainer').appendChild(row);
}
function removeNote(btn) {
    btn.closest('.note-row').remove();
}

// ─── Revision notes ─────────────────────────────────────────────────────────
let revCount = 0;
function addRevision() {
    const idx = revCount++;
    const row = document.createElement('div');
    row.className = 'rev-row';
    row.innerHTML = `<div style="display:flex;gap:8px;align-items:flex-start;">
        <textarea name="revision_notes[${idx}][text]" class="form-control" rows="2" placeholder="Deskripsi catatan revisi..."></textarea>
        <button type="button" onclick="removeRevision(this)" class="admin-btn admin-btn--danger admin-btn--sm">✕</button>
    </div>`;
    document.getElementById('revisionContainer').appendChild(row);
}
function removeRevision(btn) {
    btn.closest('.rev-row').remove();
}

// ─── Preview renderer ──────────────────────────────────────────────────────
function renderPreview() {
    const clientName  = document.querySelector('[name="client_name"]')?.value || '-';
    const packageName = document.querySelector('[name="package_name"]')?.value || '-';
    const dateDur     = document.querySelector('[name="date_duration"]')?.value || '-';
    const participants = document.getElementById('participantsInput')?.value || '0';
    const otherPct    = parseFloat(document.getElementById('otherCostPct')?.value) || 0;
    const mgmtPct     = parseFloat(document.getElementById('mgmtFeePct')?.value) || 0;

    let prodCost = 0;
    categories.forEach(cat => cat.items.forEach(i => prodCost += (i.qty||0)*(i.price||0)));
    const otherCost  = prodCost * (otherPct/100);
    const mgmtFee    = prodCost * (mgmtPct/100);
    const grandTotal = prodCost + otherCost + mgmtFee;
    const perPack    = parseInt(participants)>0 ? grandTotal/parseInt(participants) : 0;

    let catRows = '';
    categories.forEach(cat => {
        if (!cat.items.length) return;
        catRows += `<tr style="background:rgba(20,184,166,0.07)"><td colspan="7" style="font-weight:700;padding:8px 12px;color:var(--teal);">${escHtml(cat.name)}</td></tr>`;
        cat.items.forEach(item => {
            catRows += `<tr>
                <td style="padding:6px 12px;font-size:0.82rem;">${escHtml(item.component)}</td>
                <td style="padding:6px 12px;font-size:0.82rem;">${escHtml(item.description)}</td>
                <td style="padding:6px 12px;font-size:0.82rem;">${escHtml(item.remark)}</td>
                <td style="padding:6px 12px;text-align:center;font-size:0.82rem;"><span class="badge ${item.type==='V'?'badge-info':'badge-warning'}">${item.type}</span></td>
                <td style="padding:6px 12px;text-align:right;font-size:0.82rem;">${item.qty}</td>
                <td style="padding:6px 12px;text-align:right;font-size:0.82rem;">${fmtRp(item.price)}</td>
                <td style="padding:6px 12px;text-align:right;font-size:0.82rem;font-weight:700;">${fmtRp(item.qty*item.price)}</td>
            </tr>`;
        });
    });

    document.getElementById('previewContent').innerHTML = `
    <div style="border:2px solid rgba(20,184,166,0.3);border-radius:12px;overflow:hidden;">
        <div style="background:linear-gradient(135deg,#0f2a52,#14B8A6);padding:20px 24px;display:flex;justify-content:space-between;align-items:center;">
            <div style="color:#fff;font-size:1.1rem;font-weight:800;letter-spacing:1px;">TRAVINE.ID</div>
            <div style="text-align:right;color:#fff;">
                <div style="font-size:1rem;font-weight:700;text-transform:uppercase;letter-spacing:2px;">Production Cost</div>
                <div style="font-size:0.8rem;opacity:0.8;margin-top:2px;">Preview Draft</div>
            </div>
        </div>
        <div style="padding:20px 24px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Client</div>
                    <div style="font-weight:700;">${escHtml(clientName)}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Paket / Event</div>
                    <div style="font-weight:700;">${escHtml(packageName)}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Tanggal & Durasi</div>
                    <div>${escHtml(dateDur)}</div>
                </div>
                <div>
                    <div style="font-size:0.72rem;text-transform:uppercase;color:var(--text-muted);margin-bottom:4px;">Peserta</div>
                    <div>${parseInt(participants).toLocaleString('id-ID')} orang</div>
                </div>
            </div>
            <div style="overflow-x:auto;margin-bottom:20px;">
                <table style="width:100%;border-collapse:collapse;border:1px solid var(--glass-border);">
                    <thead>
                        <tr style="background:rgba(255,255,255,0.06);">
                            <th style="padding:10px 12px;text-align:left;font-size:0.75rem;text-transform:uppercase;color:var(--text-muted);">Component</th>
                            <th style="padding:10px 12px;text-align:left;font-size:0.75rem;text-transform:uppercase;color:var(--text-muted);">Deskripsi</th>
                            <th style="padding:10px 12px;text-align:left;font-size:0.75rem;text-transform:uppercase;color:var(--text-muted);">Remark</th>
                            <th style="padding:10px 12px;text-align:center;font-size:0.75rem;text-transform:uppercase;color:var(--text-muted);">Type</th>
                            <th style="padding:10px 12px;text-align:right;font-size:0.75rem;text-transform:uppercase;color:var(--text-muted);">Qty</th>
                            <th style="padding:10px 12px;text-align:right;font-size:0.75rem;text-transform:uppercase;color:var(--text-muted);">Harga @</th>
                            <th style="padding:10px 12px;text-align:right;font-size:0.75rem;text-transform:uppercase;color:var(--text-muted);">Total</th>
                        </tr>
                    </thead>
                    <tbody>${catRows || '<tr><td colspan="7" style="text-align:center;padding:20px;color:var(--text-muted);">Belum ada item</td></tr>'}</tbody>
                </table>
            </div>
            <div class="calc-summary" style="max-width:400px;margin-left:auto;">
                <div class="calc-row"><span>Biaya Produksi</span><span class="calc-val">${fmtRp(prodCost)}</span></div>
                <div class="calc-row"><span>Biaya Lain-Lain (${otherPct}%)</span><span class="calc-val">${fmtRp(otherCost)}</span></div>
                <div class="calc-row"><span>Management Fee (${mgmtPct}%)</span><span class="calc-val">${fmtRp(mgmtFee)}</span></div>
                <div class="calc-row grand"><span>GRAND TOTAL</span><span class="calc-val teal">${fmtRp(grandTotal)}</span></div>
                <div class="calc-row"><span>Price Per Pack (${parseInt(participants)} pax)</span><span class="calc-val teal">${fmtRp(perPack)}</span></div>
            </div>
            <div style="margin-top:16px;padding:10px;background:rgba(255,255,255,0.04);border-radius:8px;font-size:0.8rem;color:var(--text-muted);">
                <strong style="color:var(--text-secondary);">Legend:</strong> V = Variable Cost &nbsp;|&nbsp; F = Fixed Cost
            </div>
        </div>
    </div>`;
}

// ─── Helpers ───────────────────────────────────────────────────────────────
function fmtRp(v) {
    if (!isFinite(v)) v = 0;
    return 'Rp ' + Math.round(v).toLocaleString('id-ID');
}
function escHtml(s) {
    return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Init participant watcher
document.getElementById('participantsInput')?.addEventListener('input', recalc);
</script>
@endpush
@endsection
