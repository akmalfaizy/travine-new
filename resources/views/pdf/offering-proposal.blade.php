<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Offering Proposal - {{ $offering->offering_number }}</title>
<style>
    @page {
        margin: 24px 28px;
    }
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 10px;
        color: #1e293b;
        background: #ffffff;
        line-height: 1.4;
    }

    /* Header */
    .header-table {
        width: 100%;
        border-collapse: collapse;
        background: linear-gradient(135deg, #0f2a52 0%, #14b8a6 100%);
        padding: 16px 20px;
        border-radius: 6px;
        margin-bottom: 16px;
    }
    .header-table td {
        vertical-align: middle;
        border: none;
        padding: 0;
    }
    .header-title {
        font-size: 18px;
        font-weight: bold;
        color: #ffffff;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    .header-sub {
        font-size: 10px;
        color: rgba(255, 255, 255, 0.85);
    }
    .header-meta {
        text-align: right;
        color: #ffffff;
    }
    .header-num {
        font-size: 13px;
        font-weight: bold;
        letter-spacing: 0.5px;
    }
    .badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 8.5px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 4px;
    }
    .badge-approved { background: #d1fae5; color: #065f46; }
    .badge-sent     { background: #dbeafe; color: #1e40af; }
    .badge-draft    { background: #e2e8f0; color: #334155; }
    .badge-revised  { background: #fef3c7; color: #92400e; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
    .badge-expired  { background: #f1f5f9; color: #64748b; }

    /* Info Table */
    .info-box {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
    }
    .info-box td {
        padding: 10px 14px;
        vertical-align: top;
        border: none;
    }
    .info-cell-title {
        font-size: 8.5px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #64748b;
        margin-bottom: 6px;
        border-bottom: 1px solid #cbd5e1;
        padding-bottom: 3px;
    }
    .info-line {
        margin-bottom: 3px;
        font-size: 9.5px;
    }
    .info-line strong {
        color: #0f2a52;
        display: inline-block;
        width: 95px;
    }
    .desc-box {
        background: #f1f5f9;
        border-left: 3px solid #14b8a6;
        padding: 6px 10px;
        margin-top: 6px;
        font-size: 9px;
        color: #334155;
        border-radius: 0 4px 4px 0;
    }

    /* Category & Items */
    .cat-wrapper {
        margin-bottom: 12px;
        page-break-inside: avoid;
    }
    .cat-header {
        background: #0f2a52;
        color: #ffffff;
        padding: 5px 10px;
        font-size: 10.5px;
        font-weight: bold;
        border-radius: 4px 4px 0 0;
        width: 100%;
        border-collapse: collapse;
    }
    .cat-header td {
        padding: 0;
        border: none;
    }
    .cat-header-subtotal {
        text-align: right;
        color: #5eead4;
        font-size: 10px;
    }

    table.items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }
    table.items-table th {
        background: #f1f5f9;
        color: #475569;
        font-size: 8px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 5px 6px;
        border-bottom: 1px solid #cbd5e1;
        border-top: 1px solid #cbd5e1;
        text-align: left;
    }
    table.items-table td {
        padding: 4px 6px;
        font-size: 8.5px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    table.items-table tr:nth-child(even) td {
        background: #fafafa;
    }
    .text-center { text-align: center; }
    .text-right  { text-align: right; }
    .remark-tag {
        display: inline-block;
        font-size: 7.5px;
        padding: 1px 4px;
        border-radius: 3px;
        background: #e0f2fe;
        color: #0369a1;
        font-weight: 600;
    }
    .optional-tag {
        background: #fef3c7;
        color: #92400e;
    }

    /* Financial Summary */
    .summary-section {
        margin-top: 12px;
        page-break-inside: avoid;
        width: 100%;
        border-collapse: collapse;
    }
    .summary-section td {
        vertical-align: top;
        border: none;
        padding: 0;
    }
    table.calc-table {
        width: 100%;
        border-collapse: collapse;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
    }
    table.calc-table td {
        padding: 5px 10px;
        font-size: 9.5px;
        border-bottom: 1px solid #e2e8f0;
    }
    table.calc-table tr.total-row td {
        background: #0f2a52;
        color: #ffffff;
        font-weight: bold;
        font-size: 11px;
        border: none;
        padding: 7px 10px;
    }
    table.calc-table tr.total-row td.highlight-total {
        color: #5eead4;
        font-size: 12px;
    }
    table.calc-table tr.per-pack-row td {
        background: #f0fdf4;
        color: #166534;
        font-weight: bold;
        font-size: 10px;
        border-top: 1px solid #bbf7d0;
        padding: 6px 10px;
    }

    /* Notes & Terms */
    .notes-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 10px 12px;
        margin-top: 12px;
        page-break-inside: avoid;
    }
    .notes-title {
        font-size: 9px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #0f2a52;
        margin-bottom: 5px;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 3px;
    }
    .notes-list {
        margin-left: 14px;
        font-size: 8.5px;
        color: #334155;
    }
    .notes-list li {
        margin-bottom: 2px;
    }

    /* Recommendation */
    .rec-box {
        background: #fffbeb;
        border-left: 3px solid #f59e0b;
        padding: 6px 10px;
        margin-top: 8px;
        font-size: 8.5px;
        color: #78350f;
        border-radius: 0 4px 4px 0;
    }

    /* Signatures */
    .signature-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 18px;
        page-break-inside: avoid;
    }
    .signature-table td {
        width: 50%;
        vertical-align: top;
        text-align: center;
        border: none;
        padding: 0 20px;
    }
    .sig-role {
        font-size: 9px;
        font-weight: bold;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .sig-party {
        font-size: 10.5px;
        font-weight: bold;
        color: #0f2a52;
        margin-bottom: 45px;
    }
    .sig-line {
        border-bottom: 1px solid #94a3b8;
        width: 160px;
        margin: 0 auto 4px auto;
    }
    .sig-name {
        font-size: 9.5px;
        font-weight: bold;
        color: #1e293b;
    }
    .sig-sub {
        font-size: 8px;
        color: #64748b;
    }

    /* Footer */
    .footer {
        margin-top: 20px;
        padding-top: 8px;
        border-top: 1px solid #e2e8f0;
        text-align: center;
        font-size: 8px;
        color: #94a3b8;
    }
</style>
</head>
<body>

<!-- Header Banner -->
<table class="header-table">
    <tr>
        <td style="width: 55%;">
            <table style="border: none; border-collapse: collapse;">
                <tr>
                    <td style="padding-right: 14px; vertical-align: middle;">
                        <img src="{{ public_path('images/logo_tagline.png') }}" style="height: 38px; width: auto; display: block;" alt="travine.id">
                    </td>
                    <td style="vertical-align: middle;">
                        <div class="header-title">Production Cost Proposal</div>
                        <div class="header-sub">Document Offering & Quotation Spesifik Project</div>
                    </td>
                </tr>
            </table>
        </td>
        <td class="header-meta" style="width: 45%;">
            <div class="header-num">{{ $offering->offering_number }}</div>
            <div style="font-size: 8.5px; margin-top: 2px;">
                Tanggal: {{ $offering->created_at ? $offering->created_at->format('d M Y') : date('d M Y') }}
            </div>
            @php
                $statusKey = $offering->status ?? 'draft';
                $badgeClass = match($statusKey) {
                    'approved' => 'badge-approved',
                    'sent'     => 'badge-sent',
                    'revised'  => 'badge-revised',
                    'rejected' => 'badge-rejected',
                    'expired'  => 'badge-expired',
                    default    => 'badge-draft'
                };
            @endphp
            <span class="badge {{ $badgeClass }}">Status: {{ strtoupper($statusKey) }}</span>
        </td>
    </tr>
</table>

<!-- Client & Project Information -->
<table class="info-box">
    <tr>
        <td style="width: 50%; border-right: 1px solid #e2e8f0;">
            <div class="info-cell-title">Informasi Klien / Pemohon</div>
            <div class="info-line"><strong>Nama Klien:</strong> {{ $offering->client_name }}</div>
            <div class="info-line"><strong>Penyedia Jasa:</strong> {{ $offering->provider ?: 'travine.id' }}</div>
            <div class="info-line"><strong>PIC Klien:</strong> {{ $offering->pic_client ?: '-' }}</div>
            <div class="info-line"><strong>Kontak PIC:</strong> {{ $offering->pic_contact ?: '-' }}</div>
            @if(!empty($offering->client_email))
            <div class="info-line"><strong>Email:</strong> {{ $offering->client_email }}</div>
            @endif
        </td>
        <td style="width: 50%;">
            <div class="info-cell-title">Detail Project & Paket</div>
            <div class="info-line"><strong>Nama Paket:</strong> {{ $offering->package_name }}</div>
            <div class="info-line"><strong>Durasi / Tgl:</strong> {{ $offering->date_duration ?: '-' }}</div>
            <div class="info-line"><strong>Jumlah Peserta:</strong> {{ number_format($offering->participants) }} Orang / Pax</div>
            <div class="info-line"><strong>PIC Project (TRV):</strong> {{ $offering->pic_project ?: '-' }}</div>
        </td>
    </tr>
    @if(!empty($offering->description))
    <tr>
        <td colspan="2" style="padding-top: 0;">
            <div class="desc-box">
                <strong>Deskripsi / Ruang Lingkup:</strong> {{ $offering->description }}
            </div>
        </td>
    </tr>
    @endif
</table>

<!-- Production Cost Items by Category -->
@php $itemGlobalIndex = 1; @endphp
@forelse($offering->categories as $catIndex => $category)
    <div class="cat-wrapper">
        <table class="cat-header">
            <tr>
                <td style="width: 65%;">{{ $catIndex + 1 }}. {{ strtoupper($category->name ?: 'Kategori ' . ($catIndex + 1)) }}</td>
                <td class="cat-header-subtotal" style="width: 35%;">
                    Subtotal: {{ $category->total_cost_formatted }}
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 25px; text-align: center;">No</th>
                    <th style="width: 140px;">Komponen</th>
                    <th>Deskripsi / Spesifikasi</th>
                    <th style="width: 90px;">Keterangan</th>
                    <th style="width: 45px; text-align: center;">Tipe</th>
                    <th style="width: 38px; text-align: center;">Qty</th>
                    <th style="width: 78px; text-align: right;">Harga Satuan</th>
                    <th style="width: 82px; text-align: right;">Total Biaya</th>
                </tr>
            </thead>
            <tbody>
                @forelse($category->items as $item)
                <tr>
                    <td class="text-center" style="color: #64748b;">{{ $itemGlobalIndex++ }}</td>
                    <td><strong style="color: #0f2a52;">{{ $item->component ?: '-' }}</strong></td>
                    <td style="color: #475569;">{{ $item->description ?: '-' }}</td>
                    <td>
                        @if($item->remark)
                            @php
                                $isOpt = stripos($item->remark, 'optional') !== false;
                            @endphp
                            <span class="remark-tag {{ $isOpt ? 'optional-tag' : '' }}">{{ $item->remark }}</span>
                        @else
                            <span style="color: #94a3b8;">-</span>
                        @endif
                    </td>
                    <td class="text-center" style="color: #475569;">{{ $item->type ?: '-' }}</td>
                    <td class="text-center">{{ ($item->quantity !== null && $item->quantity !== '') ? $item->quantity : '-' }}</td>
                    <td class="text-right" style="color: #334155;">{{ ($item->unit_price !== null && $item->unit_price !== '') ? $item->unit_price_formatted : '-' }}</td>
                    <td class="text-right"><strong style="color: #0f2a52;">{{ ($item->total_cost !== null && $item->total_cost !== '') ? $item->total_cost_formatted : '-' }}</strong></td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center" style="color: #94a3b8; padding: 8px;">Tidak ada item dalam kategori ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@empty
    <div style="text-align: center; padding: 18px; color: #94a3b8; border: 1px dashed #cbd5e1; border-radius: 6px; margin-bottom: 15px; font-size: 9px;">
        Tidak ada data rincian budget / tabel kosong.
    </div>
@endforelse

<!-- Financial Summary & Calculation Breakdown -->
<table class="summary-section">
    <tr>
        <td style="width: 50%; padding-right: 10px;">
            @if(!empty($offering->notes) && is_array($offering->notes) && count($offering->notes) > 0)
            <div class="notes-box">
                <div class="notes-title">Catatan & Ketentuan Penawaran:</div>
                <ul class="notes-list">
                    @foreach($offering->notes as $note)
                        @if(trim($note) !== '')
                            <li>{{ $note }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>
            @endif

            @if(!empty($offering->recommendation))
            <div class="rec-box">
                <strong>Rekomendasi travine.id:</strong> {{ $offering->recommendation }}
            </div>
            @endif

            @if(!empty($offering->revision_notes) && is_array($offering->revision_notes) && count($offering->revision_notes) > 0)
            <div class="notes-box" style="margin-top: 8px; background: #fffdf5; border-color: #fef08a;">
                <div class="notes-title" style="color: #854d0e;">Riwayat Revisi Penawaran:</div>
                <ul class="notes-list" style="color: #713f12;">
                    @foreach($offering->revision_notes as $rev)
                        @php
                            $revText = is_array($rev) ? ($rev['text'] ?? json_encode($rev)) : $rev;
                        @endphp
                        @if(trim($revText) !== '')
                            <li>{{ $revText }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>
            @endif
        </td>

        <td style="width: 50%; padding-left: 10px;">
            <table class="calc-table">
                <tr>
                    <td style="color: #475569;">Total Biaya Produksi (Production Cost)</td>
                    <td class="text-right"><strong style="color: #0f2a52;">{{ $offering->production_cost_formatted }}</strong></td>
                </tr>
                <tr>
                    <td style="color: #475569;">
                        Other Cost ({{ number_format($offering->other_cost_percentage, 1) }}%)
                        <div style="font-size: 8px; color: #94a3b8;">Biaya kontinjensi / operasional tak terduga</div>
                    </td>
                    <td class="text-right" style="color: #334155;">{{ $offering->other_cost_formatted }}</td>
                </tr>
                <tr>
                    <td style="color: #475569;">
                        Management Fee ({{ number_format($offering->management_fee_percentage, 1) }}%)
                        <div style="font-size: 8px; color: #94a3b8;">Jasa manajemen & koordinasi travine.id</div>
                    </td>
                    <td class="text-right" style="color: #334155;">{{ $offering->management_fee_formatted }}</td>
                </tr>
                <tr class="total-row">
                    <td>GRAND TOTAL PENAWARAN</td>
                    <td class="text-right highlight-total">{{ $offering->total_amount_formatted }}</td>
                </tr>
                <tr class="per-pack-row">
                    <td>
                        Harga Per Peserta (Price / Pack)
                        <div style="font-size: 8px; font-weight: normal; color: #15803d;">Dibagi {{ number_format($offering->participants) }} orang</div>
                    </td>
                    <td class="text-right" style="font-size: 11px;">{{ $offering->price_per_pack_formatted }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Signature Section -->
<table class="signature-table">
    <tr>
        <td>
            <div class="sig-role">Disiapkan Oleh (Penyedia Jasa)</div>
            <div class="sig-party">{{ $offering->provider ?: 'travine.id' }}</div>
            <div class="sig-line"></div>
            <div class="sig-name">{{ $offering->pic_project ?: 'Project Management' }}</div>
            <div class="sig-sub">PIC Project / travine.id Representative</div>
        </td>
        <td>
            <div class="sig-role">Disetujui Oleh (Klien)</div>
            <div class="sig-party">{{ $offering->client_name }}</div>
            <div class="sig-line"></div>
            <div class="sig-name">{{ $offering->pic_client ?: 'Nama Lengkap & Jabatan' }}</div>
            <div class="sig-sub">PIC Klien / Authorized Signatory</div>
        </td>
    </tr>
</table>

<!-- Footer -->
<div class="footer">
    Dokumen Penawaran Harga (Offering Proposal) ini diterbitkan secara resmi oleh <strong>travine.id</strong> (PT Travine Nusantara).<br>
    Alamat Email: travineid@gmail.com | Website: travine.id | Dokumen ini mengikat setelah disetujui kedua belah pihak.
</div>

</body>
</html>
