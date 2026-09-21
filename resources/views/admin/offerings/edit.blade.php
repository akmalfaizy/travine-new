@extends('admin.layout')
@section('title', 'Edit Offering — ' . $offering->offering_number)

@push('styles')
<style>
.wizard-tabs { display:flex; gap:0; margin-bottom:24px; border-radius:var(--radius-sm); overflow:hidden; border:1px solid var(--glass-border); }
.wizard-tab  { flex:1; padding:12px 8px; text-align:center; font-size:0.78rem; font-weight:600; color:var(--text-muted); background:var(--glass-bg); cursor:pointer; transition:var(--transition); border-right:1px solid var(--glass-border); }
.wizard-tab:last-child { border-right:none; }
.wizard-tab.active { background:var(--teal); color:#fff; }
.wizard-tab.done   { background:rgba(20,184,166,0.15); color:var(--teal); }
.wizard-step { display:none; }
.wizard-step.active { display:block; }
.cat-block { border:1px solid var(--glass-border); border-radius:var(--radius-sm); margin-bottom:16px; overflow:hidden; }
.cat-header { display:flex; align-items:center; gap:10px; padding:12px 16px; background:rgba(255,255,255,0.04); }
.cat-header input { flex:1; background:transparent; border:none; outline:none; color:var(--text-primary); font-weight:700; font-size:0.95rem; }
.cat-items { padding:12px 16px 16px; }
.item-row { display:grid; grid-template-columns:2fr 1.5fr 1fr 80px 120px 140px 110px; gap:8px; align-items:start; padding:10px 0; border-bottom:1px solid rgba(255,255,255,0.04); }
.item-row:last-child { border-bottom:none; }
.item-row-labels { display:grid; grid-template-columns:2fr 1.5fr 1fr 80px 120px 140px 110px; gap:8px; padding:4px 0 8px; border-bottom:1px solid var(--glass-border); margin-bottom:8px; }
.item-row-labels span { font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--text-muted); }
.calc-summary { background:rgba(20,184,166,0.06); border:1px solid rgba(20,184,166,0.2); border-radius:var(--radius-sm); padding:20px 24px; }
.calc-row { display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.04); font-size:0.9rem; }
.calc-row:last-child { border-bottom:none; }
.calc-row.grand { border-top:2px solid var(--teal); margin-top:8px; padding-top:12px; font-size:1.1rem; font-weight:800; color:var(--text-primary); }
.calc-val { font-weight:700; font-family:'Courier New', monospace; }
.calc-val.teal { color:var(--teal); }
.note-row { display:flex; gap:8px; margin-bottom:8px; align-items:center; }
.note-row input { flex:1; }
.rev-row { background:rgba(255,255,255,0.03); border:1px solid var(--glass-border); border-radius:var(--radius-sm); padding:12px; margin-bottom:10px; }
.master-picker { position:relative; }
.master-list { position:absolute; top:100%; left:0; right:0; background:#0f1a2e; border:1px solid var(--glass-border); border-radius:var(--radius-sm); max-height:260px; overflow-y:auto; z-index:200; display:none; }
.master-list.open { display:block; }
.master-item-opt { padding:10px 14px; cursor:pointer; font-size:0.88rem; border-bottom:1px solid rgba(255,255,255,0.04); }
.master-item-opt:hover { background:rgba(20,184,166,0.1); color:var(--teal); }
.master-item-opt small { display:block; color:var(--text-muted); font-size:0.75rem; margin-top:2px; }
</style>
@endpush

@section('content')
<div class="admin-topbar">
    <div>
        <h1>Edit Offering</h1>
        <p style="color:var(--text-muted);font-size:0.85rem;margin-top:4px;">{{ $offering->offering_number }} · {{ $offering->package_name }}</p>
    </div>
    <a href="{{ route('admin.offerings.show', $offering) }}" class="admin-btn admin-btn--secondary">← Kembali</a>
</div>

<form id="offeringForm" method="POST" action="{{ route('admin.offerings.update', $offering) }}">
@csrf @method('PUT')

<div class="wizard-tabs" id="wizardTabs">
    <div class="wizard-tab active" data-step="1">① Client & Project</div>
    <div class="wizard-tab" data-step="2">② Budget Items</div>
    <div class="wizard-tab" data-step="3">③ Biaya Tambahan</div>
    <div class="wizard-tab" data-step="4">④ Catatan & Rekomendasi</div>
    <div class="wizard-tab" data-step="5">⑤ Preview & Simpan</div>
</div>

{{-- STEP 1 --}}
<div class="wizard-step active" id="step1">
    <div class="admin-card">
        <h3 style="font-weight:700;margin-bottom:20px;">Informasi Klien & Proyek</h3>
        <div class="admin-grid admin-grid--2">
            <div class="form-group">
                <label class="form-label">Nama Klien / Perusahaan *</label>
                <input type="text" name="client_name" class="form-control" value="{{ old('client_name', $offering->client_name) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Provider / Penyelenggara</label>
                <input type="text" name="provider" class="form-control" value="{{ old('provider', $offering->provider) }}">
            </div>
            <div class="form-group">
                <label class="form-label">PIC Client</label>
                <input type="text" name="pic_client" class="form-control" value="{{ old('pic_client', $offering->pic_client) }}">
            </div>
            <div class="form-group">
                <label class="form-label">PIC Project (Internal)</label>
                <input type="text" name="pic_project" class="form-control" value="{{ old('pic_project', $offering->pic_project) }}">
            </div>
            <div class="form-group">
                <label class="form-label">No. Kontak PIC</label>
                <input type="text" name="pic_contact" class="form-control" value="{{ old('pic_contact', $offering->pic_contact) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Email Klien / PIC</label>
                <input type="email" name="client_email" class="form-control" value="{{ old('client_email', $offering->client_email) }}" placeholder="procurement@perusahaan.com">
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;">Digunakan untuk pengiriman proposal via email</div>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Paket / Event *</label>
                <input type="text" name="package_name" class="form-control" value="{{ old('package_name', $offering->package_name) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal & Durasi</label>
                <input type="text" name="date_duration" class="form-control" value="{{ old('date_duration', $offering->date_duration) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Jumlah Peserta *</label>
                <input type="number" name="participants" id="participantsInput" class="form-control" value="{{ old('participants', $offering->participants) }}" min="1" required>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Deskripsi Singkat</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $offering->description) }}</textarea>
        </div>
        <div style="display:flex;justify-content:flex-end;margin-top:8px;">
            <button type="button" class="admin-btn admin-btn--primary" onclick="goStep(2)">Lanjut → Budget Items</button>
        </div>
    </div>
</div>

{{-- STEP 2 --}}
<div class="wizard-step" id="step2">
    <div class="admin-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-weight:700;">Detail Item Budget</h3>
            <div style="display:flex;gap:8px;">
                <div class="master-picker" id="masterPicker">
                    <button type="button" class="admin-btn admin-btn--secondary" onclick="toggleMasterPicker()">📋 Master Item</button>
                    <div class="master-list" id="masterList">
                        <div style="padding:8px 14px;font-size:0.75rem;font-weight:700;color:var(--text-muted);border-bottom:1px solid var(--glass-border);">Klik item untuk menambahkan:</div>
                        @foreach($masterByCategory as $catName => $items)
                        <div style="padding:6px 14px;font-size:0.75rem;font-weight:700;text-transform:uppercase;color:var(--teal);background:rgba(20,184,166,0.06);">{{ $catName }}</div>
                        @foreach($items as $mi)
                        <div class="master-item-opt" onclick="addMasterItem('{{ addslashes($catName) }}', '{{ addslashes($mi->component) }}', '{{ addslashes($mi->description ?? '') }}', '{{ $mi->default_type }}', '{{ addslashes($mi->default_remark) }}', {{ $mi->default_price }})">
                            {{ $mi->component }}
                            <small>{{ $mi->default_remark }} · {{ $mi->default_type === 'V' ? 'Variable' : 'Fixed' }}</small>
                        </div>
                        @endforeach
                        @endforeach
                    </div>
                </div>
                <button type="button" class="admin-btn admin-btn--primary" onclick="addCategory()">+ Tambah Kategori</button>
            </div>
        </div>
        <div id="categoriesContainer"></div>
        <div id="emptyState" style="display:none;text-align:center;padding:40px;color:var(--text-muted);border:2px dashed var(--glass-border);border-radius:var(--radius-sm);">
            <p>Belum ada kategori. Klik <strong>Tambah Kategori</strong>.</p>
        </div>
        <div style="display:flex;justify-content:space-between;margin-top:20px;">
            <button type="button" class="admin-btn admin-btn--secondary" onclick="goStep(1)">← Kembali</button>
            <button type="button" class="admin-btn admin-btn--primary" onclick="goStep(3)">Lanjut → Biaya Tambahan</button>
        </div>
    </div>
</div>

{{-- STEP 3 --}}
<div class="wizard-step" id="step3">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
        <div class="admin-card">
            <h3 style="font-weight:700;margin-bottom:20px;">Persentase Biaya</h3>
            <div class="form-group">
                <label class="form-label">Biaya Lain-Lain (%)</label>
                <input type="number" name="other_cost_percentage" id="otherCostPct" class="form-control" value="{{ old('other_cost_percentage', $offering->other_cost_percentage) }}" min="0" max="100" step="0.01" oninput="recalc()">
            </div>
            <div class="form-group">
                <label class="form-label">Management Fee (%)</label>
                <input type="number" name="management_fee_percentage" id="mgmtFeePct" class="form-control" value="{{ old('management_fee_percentage', $offering->management_fee_percentage) }}" min="0" max="100" step="0.01" oninput="recalc()">
            </div>
        </div>
        <div class="calc-summary">
            <h3 style="font-weight:700;margin-bottom:16px;">Ringkasan Biaya</h3>
            <div class="calc-row"><span>Biaya Produksi</span><span class="calc-val" id="dispProductionCost">Rp 0</span></div>
            <div class="calc-row"><span>Biaya Lain-Lain (<span id="dispOtherPct">5</span>%)</span><span class="calc-val" id="dispOtherCost">Rp 0</span></div>
            <div class="calc-row"><span>Management Fee (<span id="dispMgmtPct">15</span>%)</span><span class="calc-val" id="dispMgmtFee">Rp 0</span></div>
            <div class="calc-row grand"><span>GRAND TOTAL</span><span class="calc-val teal" id="dispGrandTotal">Rp 0</span></div>
            <div class="calc-row"><span>Price Per Pack (<span id="dispParticipants">1</span> pax)</span><span class="calc-val teal" id="dispPricePerPack">Rp 0</span></div>
        </div>
    </div>
    <div style="display:flex;justify-content:space-between;margin-top:20px;">
        <button type="button" class="admin-btn admin-btn--secondary" onclick="goStep(2)">← Kembali</button>
        <button type="button" class="admin-btn admin-btn--primary" onclick="goStep(4)">Lanjut → Catatan</button>
    </div>
</div>

{{-- STEP 4 --}}
<div class="wizard-step" id="step4">
    <div class="admin-card">
        <h3 style="font-weight:700;margin-bottom:20px;">Keterangan, Revisi & Rekomendasi</h3>
        <div class="form-group">
            <label class="form-label" style="margin-bottom:10px;">Keterangan</label>
            <div id="notesContainer">
                @php $existingNotes = old('notes', $offering->notes ?? []); @endphp
                @forelse($existingNotes as $n)
                <div class="note-row">
                    <input type="text" name="notes[]" class="form-control" value="{{ $n }}">
                    <button type="button" onclick="removeNote(this)" class="admin-btn admin-btn--danger admin-btn--sm">✕</button>
                </div>
                @empty
                <div class="note-row">
                    <input type="text" name="notes[]" class="form-control" placeholder="Contoh: Variable Cost (V)">
                    <button type="button" onclick="removeNote(this)" class="admin-btn admin-btn--danger admin-btn--sm">✕</button>
                </div>
                @endforelse
            </div>
            <button type="button" onclick="addNote()" class="admin-btn admin-btn--secondary admin-btn--sm" style="margin-top:8px;">+ Tambah Keterangan</button>
        </div>

        <div class="form-group" style="margin-top:24px;">
            <label class="form-label" style="margin-bottom:10px;">Catatan Revisi</label>
            <div id="revisionContainer">
                @php $existingRevisions = old('revision_notes', $offering->revision_notes ?? []); $revCount = count($existingRevisions); @endphp
                @foreach($existingRevisions as $i => $rn)
                <div class="rev-row">
                    <div style="display:flex;gap:8px;align-items:flex-start;">
                        <textarea name="revision_notes[{{ $i }}][text]" class="form-control" rows="2">{{ $rn['text'] ?? '' }}</textarea>
                        <button type="button" onclick="removeRevision(this)" class="admin-btn admin-btn--danger admin-btn--sm">✕</button>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" onclick="addRevision()" class="admin-btn admin-btn--secondary admin-btn--sm" style="margin-top:8px;">+ Tambah Catatan Revisi</button>
        </div>

        <div class="form-group" style="margin-top:24px;">
            <label class="form-label">Rekomendasi (Opsional)</label>
            <textarea name="recommendation" class="form-control" rows="4">{{ old('recommendation', $offering->recommendation) }}</textarea>
        </div>

        <div style="display:flex;justify-content:space-between;margin-top:20px;">
            <button type="button" class="admin-btn admin-btn--secondary" onclick="goStep(3)">← Kembali</button>
            <button type="button" class="admin-btn admin-btn--primary" onclick="goStep(5)">Lanjut → Preview</button>
        </div>
    </div>
</div>

{{-- STEP 5 --}}
<div class="wizard-step" id="step5">
    <div class="admin-card" id="previewContent">
        <p style="text-align:center;color:var(--text-muted);">Preview akan muncul saat membuka tab ini.</p>
    </div>
    <div style="display:flex;justify-content:space-between;margin-top:20px;">
        <button type="button" class="admin-btn admin-btn--secondary" onclick="goStep(4)">← Kembali</button>
        <button type="submit" class="admin-btn admin-btn--primary" style="min-width:140px;">💾 Simpan Perubahan</button>
    </div>
</div>

</form>

@push('scripts')
<script>
@php
    $categoriesJson = $offering->categories->map(function($c) {
        return [
            'id'    => $c->id,
            'name'  => $c->name,
            'items' => $c->items->map(function($i) {
                return [
                    'id'          => $i->id,
                    'component'   => $i->component,
                    'description' => $i->description ?? '',
                    'remark'      => $i->remark,
                    'type'        => $i->type,
                    'qty'         => $i->quantity,
                    'price'       => $i->unit_price,
                ];
            })->values(),
        ];
    })->values();
@endphp
let categories = {!! json_encode($categoriesJson) !!};

// Remap to JS IDs
let catCounter  = categories.reduce((m,c) => Math.max(m,c.id), 0);
let itemCounter = 0;
categories.forEach(cat => {
    cat.items.forEach(item => {
        item.id = ++itemCounter;
    });
});

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

function addCategory(name) {
    const id = ++catCounter;
    categories.push({ id, name: name||'', items:[] });
    renderCategories();
    document.getElementById('emptyState').style.display = 'none';
}
function removeCategory(id) {
    if (!confirm('Hapus kategori ini?')) return;
    categories = categories.filter(c => c.id !== id);
    renderCategories();
    if (!categories.length) document.getElementById('emptyState').style.display = '';
}
function addItem(catId) {
    const cat = categories.find(c => c.id === catId);
    if (cat) { cat.items.push({ id:++itemCounter, component:'', description:'', remark:'Included (Wajib)', type:'V', qty:1, price:0 }); renderCategories(); }
}
function removeItem(catId, itemId) {
    const cat = categories.find(c => c.id === catId);
    if (cat) { cat.items = cat.items.filter(i => i.id !== itemId); renderCategories(); recalc(); }
}
function updateCatName(catId, val) {
    const cat = categories.find(c => c.id === catId); if (cat) cat.name = val;
}
function updateItem(catId, itemId, field, val) {
    const cat = categories.find(c => c.id === catId); if (!cat) return;
    const item = cat.items.find(i => i.id === itemId); if (!item) return;
    if (field === 'qty' || field === 'price') val = parseFloat(val)||0;
    item[field] = val; recalc();
}
function renderCategories() {
    const container = document.getElementById('categoriesContainer');
    container.innerHTML = '';
    categories.forEach((cat, ci) => {
        const div = document.createElement('div');
        div.className = 'cat-block';
        div.innerHTML = `<div class="cat-header">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity:0.4;flex-shrink:0;"><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/></svg>
            <input type="text" placeholder="Nama Kategori" value="${escHtml(cat.name)}" oninput="updateCatName(${cat.id}, this.value)">
            <button type="button" onclick="removeCategory(${cat.id})" class="admin-btn admin-btn--danger admin-btn--sm">✕</button>
        </div>
        <div class="cat-items">
            ${cat.items.length?`<div class="item-row-labels"><span>Component</span><span>Deskripsi</span><span>Remark</span><span>Type</span><span>Qty</span><span>Harga @</span><span>Total</span></div>`:''}
            ${cat.items.map(item => renderItemRow(cat.id, item)).join('')}
            <button type="button" onclick="addItem(${cat.id})" class="admin-btn admin-btn--secondary admin-btn--sm" style="margin-top:10px;">+ Tambah Item</button>
        </div>`;
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
    return `<div class="item-row">
        <input class="form-control" style="font-size:0.85rem;" placeholder="Component" value="${escHtml(item.component)}" oninput="updateItem(${catId},${item.id},'component',this.value)">
        <input class="form-control" style="font-size:0.85rem;" placeholder="Deskripsi" value="${escHtml(item.description)}" oninput="updateItem(${catId},${item.id},'description',this.value)">
        <input class="form-control" style="font-size:0.85rem;" placeholder="Remark" value="${escHtml(item.remark)}" oninput="updateItem(${catId},${item.id},'remark',this.value)">
        <select class="form-control" style="font-size:0.85rem;" onchange="updateItem(${catId},${item.id},'type',this.value)">
            <option value="V" ${item.type==='V'?'selected':''}>V</option>
            <option value="F" ${item.type==='F'?'selected':''}>F</option>
        </select>
        <input type="number" class="form-control" style="font-size:0.85rem;" value="${item.qty}" min="0" step="any" oninput="updateItem(${catId},${item.id},'qty',this.value)">
        <input type="number" class="form-control" style="font-size:0.85rem;" value="${item.price}" min="0" step="any" oninput="updateItem(${catId},${item.id},'price',this.value)">
        <div style="display:flex;align-items:center;gap:6px;">
            <span style="font-size:0.85rem;font-weight:700;color:var(--teal);font-family:monospace;" id="itemtotal-${item.id}">${fmtRp(item.qty*item.price)}</span>
            <button type="button" onclick="removeItem(${catId},${item.id})" class="admin-btn admin-btn--danger admin-btn--sm">✕</button>
        </div>
    </div>`;
}
function toggleMasterPicker() { document.getElementById('masterList').classList.toggle('open'); }
document.addEventListener('click', e => { if (!document.getElementById('masterPicker').contains(e.target)) document.getElementById('masterList').classList.remove('open'); });
function addMasterItem(catName, component, description, type, remark, price) {
    let cat = categories.find(c => c.name.toLowerCase() === catName.toLowerCase());
    if (!cat) { addCategory(catName); cat = categories[categories.length-1]; }
    cat.items.push({ id:++itemCounter, component, description, remark, type, qty:1, price });
    renderCategories();
    document.getElementById('masterList').classList.remove('open');
    document.getElementById('emptyState').style.display = 'none';
}
function recalc() {
    let prodCost = 0;
    categories.forEach(cat => cat.items.forEach(item => { prodCost += (item.qty||0)*(item.price||0); }));
    const participants = parseInt(document.getElementById('participantsInput')?.value)||1;
    const otherPct = parseFloat(document.getElementById('otherCostPct')?.value)||0;
    const mgmtPct  = parseFloat(document.getElementById('mgmtFeePct')?.value)||0;
    const otherCost = prodCost*(otherPct/100);
    const mgmtFee   = prodCost*(mgmtPct/100);
    const grandTotal = prodCost+otherCost+mgmtFee;
    const perPack   = participants>0 ? grandTotal/participants : 0;
    categories.forEach(cat => cat.items.forEach(item => { const el=document.getElementById('itemtotal-'+item.id); if(el) el.textContent=fmtRp((item.qty||0)*(item.price||0)); }));
    const set=(id,val)=>{ const el=document.getElementById(id); if(el) el.textContent=val; };
    set('dispProductionCost',fmtRp(prodCost)); set('dispOtherCost',fmtRp(otherCost)); set('dispMgmtFee',fmtRp(mgmtFee)); set('dispGrandTotal',fmtRp(grandTotal)); set('dispPricePerPack',fmtRp(perPack));
    set('dispOtherPct',otherPct); set('dispMgmtPct',mgmtPct); set('dispParticipants',participants);
}
function addNote() {
    const row=document.createElement('div'); row.className='note-row';
    row.innerHTML=`<input type="text" name="notes[]" class="form-control" placeholder="Keterangan..."><button type="button" onclick="removeNote(this)" class="admin-btn admin-btn--danger admin-btn--sm">✕</button>`;
    document.getElementById('notesContainer').appendChild(row);
}
function removeNote(btn) { btn.closest('.note-row').remove(); }
let revCount = {{ count($offering->revision_notes ?? []) }};
function addRevision() {
    const idx=revCount++; const row=document.createElement('div'); row.className='rev-row';
    row.innerHTML=`<div style="display:flex;gap:8px;align-items:flex-start;"><textarea name="revision_notes[${idx}][text]" class="form-control" rows="2" placeholder="Catatan revisi..."></textarea><button type="button" onclick="removeRevision(this)" class="admin-btn admin-btn--danger admin-btn--sm">✕</button></div>`;
    document.getElementById('revisionContainer').appendChild(row);
}
function removeRevision(btn) { btn.closest('.rev-row').remove(); }
function renderPreview() {
    let prodCost=0; categories.forEach(cat=>cat.items.forEach(i=>prodCost+=(i.qty||0)*(i.price||0)));
    const participants=parseInt(document.getElementById('participantsInput')?.value)||1;
    const otherPct=parseFloat(document.getElementById('otherCostPct')?.value)||0;
    const mgmtPct=parseFloat(document.getElementById('mgmtFeePct')?.value)||0;
    const otherCost=prodCost*(otherPct/100), mgmtFee=prodCost*(mgmtPct/100), grandTotal=prodCost+otherCost+mgmtFee, perPack=participants>0?grandTotal/participants:0;
    let catRows='';
    categories.forEach(cat => { if (!cat.items.length) return; catRows+=`<tr style="background:rgba(20,184,166,0.07)"><td colspan="7" style="font-weight:700;padding:8px 12px;color:var(--teal);">${escHtml(cat.name)}</td></tr>`; cat.items.forEach(item => { catRows+=`<tr><td style="padding:6px 12px;font-size:0.82rem;">${escHtml(item.component)}</td><td style="padding:6px 12px;font-size:0.82rem;">${escHtml(item.description)}</td><td style="padding:6px 12px;font-size:0.82rem;">${escHtml(item.remark)}</td><td style="padding:6px 12px;text-align:center;font-size:0.82rem;"><span class="badge ${item.type==='V'?'badge-info':'badge-warning'}">${item.type}</span></td><td style="padding:6px 12px;text-align:right;font-size:0.82rem;">${item.qty}</td><td style="padding:6px 12px;text-align:right;font-size:0.82rem;">${fmtRp(item.price)}</td><td style="padding:6px 12px;text-align:right;font-size:0.82rem;font-weight:700;">${fmtRp(item.qty*item.price)}</td></tr>`; }); });
    document.getElementById('previewContent').innerHTML=`<div style="border:2px solid rgba(20,184,166,0.3);border-radius:12px;overflow:hidden;"><div style="background:linear-gradient(135deg,#0f2a52,#14B8A6);padding:20px 24px;display:flex;justify-content:space-between;align-items:center;"><div style="color:#fff;font-size:1.1rem;font-weight:800;">TRAVINE.ID</div><div style="text-align:right;color:#fff;"><div style="font-size:1rem;font-weight:700;text-transform:uppercase;letter-spacing:2px;">Production Cost</div><div style="font-size:0.8rem;opacity:0.8;">{{ $offering->offering_number }}</div></div></div><div style="padding:20px 24px;overflow-x:auto;"><table style="width:100%;border-collapse:collapse;border:1px solid var(--glass-border);margin-bottom:20px;"><thead><tr style="background:rgba(255,255,255,0.06);"><th style="padding:10px 12px;text-align:left;font-size:0.75rem;text-transform:uppercase;color:var(--text-muted);">Component</th><th style="padding:10px 12px;text-align:left;font-size:0.75rem;text-transform:uppercase;color:var(--text-muted);">Deskripsi</th><th style="padding:10px 12px;text-align:left;font-size:0.75rem;text-transform:uppercase;color:var(--text-muted);">Remark</th><th style="padding:10px 12px;text-align:center;font-size:0.75rem;text-transform:uppercase;color:var(--text-muted);">Type</th><th style="padding:10px 12px;text-align:right;font-size:0.75rem;text-transform:uppercase;color:var(--text-muted);">Qty</th><th style="padding:10px 12px;text-align:right;font-size:0.75rem;text-transform:uppercase;color:var(--text-muted);">Harga @</th><th style="padding:10px 12px;text-align:right;font-size:0.75rem;text-transform:uppercase;color:var(--text-muted);">Total</th></tr></thead><tbody>${catRows||'<tr><td colspan="7" style="text-align:center;padding:20px;color:var(--text-muted);">Belum ada item</td></tr>'}</tbody></table><div class="calc-summary" style="max-width:400px;margin-left:auto;"><div class="calc-row"><span>Biaya Produksi</span><span class="calc-val">${fmtRp(prodCost)}</span></div><div class="calc-row"><span>Biaya Lain-Lain (${otherPct}%)</span><span class="calc-val">${fmtRp(otherCost)}</span></div><div class="calc-row"><span>Management Fee (${mgmtPct}%)</span><span class="calc-val">${fmtRp(mgmtFee)}</span></div><div class="calc-row grand"><span>GRAND TOTAL</span><span class="calc-val teal">${fmtRp(grandTotal)}</span></div><div class="calc-row"><span>Price Per Pack (${participants} pax)</span><span class="calc-val teal">${fmtRp(perPack)}</span></div></div></div></div>`;
}
function fmtRp(v) { if (!isFinite(v)) v=0; return 'Rp '+Math.round(v).toLocaleString('id-ID'); }
function escHtml(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

document.getElementById('participantsInput')?.addEventListener('input', recalc);

// Init render
renderCategories();
if (categories.length) document.getElementById('emptyState').style.display = 'none';
</script>
@endpush
@endsection
