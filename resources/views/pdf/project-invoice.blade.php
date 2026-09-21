<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Invoice - {{ $projectInvoice->invoice_number }}</title>
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
        font-size: 22px;
        font-weight: bold;
        color: #ffffff;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    .header-sub {
        font-size: 9.5px;
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
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 8.5px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 4px;
    }
    .badge-paid      { background: #d1fae5; color: #065f46; }
    .badge-partial   { background: #fef3c7; color: #92400e; }
    .badge-unpaid    { background: #fee2e2; color: #991b1b; }
    .badge-overdue   { background: #fecaca; color: #7f1d1d; }
    .badge-cancelled { background: #f1f5f9; color: #64748b; }

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
        width: 100px;
    }

    /* Items */
    .cat-wrapper {
        margin-bottom: 12px;
        page-break-inside: avoid;
    }
    .cat-header {
        background: #0f2a52;
        color: #ffffff;
        padding: 4px 8px;
        font-size: 9.5px;
        font-weight: bold;
        border-radius: 4px 4px 0 0;
        width: 100%;
        border-collapse: collapse;
    }
    .cat-header td {
        padding: 0;
        border: none;
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

    /* Financial Summary */
    .bottom-section {
        margin-top: 12px;
        page-break-inside: avoid;
        width: 100%;
        border-collapse: collapse;
    }
    .bottom-section td {
        vertical-align: top;
        border: none;
        padding: 0;
    }
    .bank-card {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 6px;
        padding: 10px 12px;
        margin-bottom: 10px;
    }
    .bank-title {
        font-size: 9px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #166534;
        margin-bottom: 5px;
        border-bottom: 1px solid #bbf7d0;
        padding-bottom: 3px;
    }
    .bank-line {
        margin-bottom: 3px;
        font-size: 9px;
        color: #14532d;
    }
    .bank-line strong {
        display: inline-block;
        width: 90px;
        color: #0f2a52;
    }
    .notes-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 8.5px;
        color: #475569;
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
    table.calc-table tr.sub-row td {
        color: #475569;
    }
    table.calc-table tr.gross-row td {
        background: #f1f5f9;
        font-weight: bold;
        color: #0f2a52;
    }
    table.calc-table tr.pph-row td {
        color: #dc2626;
    }
    table.calc-table tr.net-row td {
        background: #0f2a52;
        color: #ffffff;
        font-weight: bold;
        font-size: 11px;
    }
    table.calc-table tr.net-row td.highlight-net {
        color: #5eead4;
        font-size: 11.5px;
    }
    table.calc-table tr.paid-row td {
        color: #16a34a;
        font-weight: bold;
    }
    table.calc-table tr.balance-row td {
        background: #fef2f2;
        color: #b91c1c;
        font-weight: bold;
        border-top: 2px solid #f87171;
    }
    table.calc-table tr.balance-paid-row td {
        background: #f0fdf4;
        color: #15803d;
        font-weight: bold;
        border-top: 2px solid #4ade80;
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
        font-size: 8.5px;
        font-weight: bold;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .sig-party {
        font-size: 10px;
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
        margin-top: 18px;
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
                        <div class="header-title">INVOICE</div>
                        <div class="header-sub">Tagihan Resmi Project & Layanan travine.id</div>
                    </td>
                </tr>
            </table>
        </td>
        <td class="header-meta" style="width: 45%;">
            <div class="header-num">{{ $projectInvoice->invoice_number }}</div>
            <div style="font-size: 8.5px; margin-top: 2px;">
                Tgl Terbit: {{ $projectInvoice->issue_date ? $projectInvoice->issue_date->format('d M Y') : date('d M Y') }} &nbsp;|&nbsp; 
                Jatuh Tempo: {{ $projectInvoice->due_date ? $projectInvoice->due_date->format('d M Y') : '-' }}
            </div>
            @if($projectInvoice->offering)
                <div style="font-size: 8px; opacity: 0.85; margin-top: 1px;">
                    Ref Offering: {{ $projectInvoice->offering->offering_number }}
                </div>
            @endif
            @php
                $statusKey = $projectInvoice->payment_status ?? 'unpaid';
                $badgeClass = match($statusKey) {
                    'paid'      => 'badge-paid',
                    'partial'   => 'badge-partial',
                    'overdue'   => 'badge-overdue',
                    'cancelled' => 'badge-cancelled',
                    default     => 'badge-unpaid'
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
            <div class="info-cell-title">Ditagihkan Kepada (Bill To)</div>
            <div class="info-line"><strong>Klien / Perusahaan:</strong> {{ $projectInvoice->client_company ?: $projectInvoice->client_name }}</div>
            @if($projectInvoice->client_company && $projectInvoice->client_name !== $projectInvoice->client_company)
                <div class="info-line"><strong>Nama Kontak:</strong> {{ $projectInvoice->client_name }}</div>
            @endif
            <div class="info-line"><strong>PIC Klien:</strong> {{ $projectInvoice->pic_client ?: '-' }}</div>
            <div class="info-line"><strong>Kontak PIC:</strong> {{ $projectInvoice->pic_contact ?: '-' }}</div>
            @if(!empty($projectInvoice->client_email))
            <div class="info-line"><strong>Email:</strong> {{ $projectInvoice->client_email }}</div>
            @endif
        </td>
        <td style="width: 50%;">
            <div class="info-cell-title">Detail Project</div>
            <div class="info-line"><strong>Nama Project:</strong> {{ $projectInvoice->project_name }}</div>
            <div class="info-line"><strong>No. Invoice:</strong> {{ $projectInvoice->invoice_number }}</div>
            <div class="info-line"><strong>Jatuh Tempo:</strong> {{ $projectInvoice->due_date ? $projectInvoice->due_date->format('d M Y') : '-' }}</div>
            @if(!empty($projectInvoice->description))
                <div style="font-size: 8.5px; color: #475569; margin-top: 3px; font-style: italic;">
                    {{ $projectInvoice->description }}
                </div>
            @endif
        </td>
    </tr>
</table>

<!-- Invoice Items Table (Grouped by Category) -->
@php
    $groupedItems = $projectInvoice->items->groupBy('category_name');
    $itemGlobalIndex = 1;
@endphp

@foreach($groupedItems as $categoryName => $items)
    <div class="cat-wrapper">
        <table class="cat-header">
            <tr>
                <td style="width: 70%;">{{ strtoupper($categoryName ?: 'Item Project') }}</td>
                <td style="width: 30%; text-align: right; color: #5eead4;">
                    Rp {{ number_format($items->sum('total_cost'), 0, ',', '.') }}
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 25px; text-align: center;">No</th>
                    <th style="width: 150px;">Komponen</th>
                    <th>Deskripsi / Spesifikasi</th>
                    <th style="width: 45px; text-align: center;">Tipe</th>
                    <th style="width: 38px; text-align: center;">Qty</th>
                    <th style="width: 80px; text-align: right;">Harga Satuan</th>
                    <th style="width: 85px; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td class="text-center" style="color: #64748b;">{{ $itemGlobalIndex++ }}</td>
                    <td><strong style="color: #0f2a52;">{{ $item->component }}</strong></td>
                    <td style="color: #475569;">
                        {{ $item->description ?: '-' }}
                        @if($item->remark)
                            <span style="font-size: 7.5px; color: #0284c7; margin-left: 4px;">({{ $item->remark }})</span>
                        @endif
                    </td>
                    <td class="text-center" style="color: #475569;">{{ $item->type ?: 'Pack' }}</td>
                    <td class="text-center"><strong>{{ $item->quantity }}</strong></td>
                    <td class="text-right" style="color: #334155;">{{ $item->unit_price_formatted }}</td>
                    <td class="text-right"><strong style="color: #0f2a52;">{{ $item->total_cost_formatted }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endforeach

<!-- Payment & Financial Summary -->
<table class="bottom-section">
    <tr>
        <!-- Left: Bank Details & Notes -->
        <td style="width: 48%; padding-right: 12px;">
            <div class="bank-card">
                <div class="bank-title">Informasi Pembayaran (Transfer Bank):</div>
                <div class="bank-line"><strong>Bank:</strong> {{ $projectInvoice->bank_name ?: 'BCA (Bank Central Asia)' }}</div>
                <div class="bank-line"><strong>No. Rekening:</strong> {{ $projectInvoice->bank_account ?: '842-089-1234' }}</div>
                <div class="bank-line"><strong>Atas Nama:</strong> {{ $projectInvoice->bank_holder ?: 'PT Travine Nusantara' }}</div>
                <div style="font-size: 8px; color: #166534; margin-top: 4px;">
                    * Mohon cantumkan No. Invoice (<strong>{{ $projectInvoice->invoice_number }}</strong>) pada berita transfer.
                </div>
            </div>

            @if(!empty($projectInvoice->notes))
            <div class="notes-box">
                <strong style="color: #0f2a52; font-size: 8.5px; display: block; margin-bottom: 2px;">Catatan:</strong>
                {{ $projectInvoice->notes }}
            </div>
            @endif
        </td>

        <!-- Right: Calculation Breakdown -->
        <td style="width: 52%; padding-left: 12px;">
            <table class="calc-table">
                <tr class="sub-row">
                    <td>Biaya Produksi (Subtotal)</td>
                    <td class="text-right">{{ $projectInvoice->subtotal_formatted }}</td>
                </tr>
                <tr class="sub-row">
                    <td>Other Cost (5%)</td>
                    <td class="text-right">{{ $projectInvoice->other_cost_formatted }}</td>
                </tr>
                <tr class="sub-row">
                    <td>Management Fee (15%)</td>
                    <td class="text-right">{{ $projectInvoice->management_fee_formatted }}</td>
                </tr>
                <tr class="gross-row">
                    <td>Total Tagihan Kotor (Gross Total)</td>
                    <td class="text-right">{{ $projectInvoice->gross_total_formatted }}</td>
                </tr>

                @if($projectInvoice->include_pph_deduction && $projectInvoice->pph_amount > 0)
                <tr class="pph-row">
                    <td>
                        Potongan PPh ({{ number_format($projectInvoice->pph_percentage, 1) }}%)
                        <div style="font-size: 7.5px; color: #b91c1c;">Dipotong oleh Klien (Bukti Potong PPh diserahkan)</div>
                    </td>
                    <td class="text-right">-{{ $projectInvoice->pph_amount_formatted }}</td>
                </tr>
                <tr class="net-row">
                    <td>TOTAL DITERIMA BERSIH (NET AMOUNT)</td>
                    <td class="text-right highlight-net">{{ $projectInvoice->net_amount_formatted }}</td>
                </tr>
                @endif

                <tr class="paid-row">
                    <td>Jumlah Sudah Dibayar (Amount Paid)</td>
                    <td class="text-right">{{ $projectInvoice->amount_paid_formatted }}</td>
                </tr>

                @if($projectInvoice->payment_status === 'paid' || $projectInvoice->remaining_balance <= 0)
                <tr class="balance-paid-row">
                    <td>SISA TAGIHAN (LUNAS)</td>
                    <td class="text-right">Rp 0 (LUNAS)</td>
                </tr>
                @else
                <tr class="balance-row">
                    <td>SISA TAGIHAN YANG HARUS DIBAYAR</td>
                    <td class="text-right">{{ $projectInvoice->remaining_balance_formatted }}</td>
                </tr>
                @endif
            </table>
        </td>
    </tr>
</table>

<!-- Signature Section -->
<table class="signature-table">
    <tr>
        <td>
            <div class="sig-role">Penerima Tagihan (Klien)</div>
            <div class="sig-party">{{ $projectInvoice->client_company ?: $projectInvoice->client_name }}</div>
            <div class="sig-line"></div>
            <div class="sig-name">{{ $projectInvoice->pic_client ?: 'Finance / Management' }}</div>
            <div class="sig-sub">Authorized Signatory</div>
        </td>
        <td>
            <div class="sig-role">Penerbit Invoice (Penyedia Jasa)</div>
            <div class="sig-party">travine.id (PT Travine Nusantara)</div>
            <div class="sig-line"></div>
            <div class="sig-name">Finance & Accounting Department</div>
            <div class="sig-sub">travineid@gmail.com</div>
        </td>
    </tr>
</table>

<!-- Footer -->
<div class="footer">
    Invoice ini merupakan bukti tagihan sah yang diterbitkan oleh <strong>travine.id</strong> (PT Travine Nusantara).<br>
    Bukti transfer dan bukti potong PPh (jika ada) dapat dikirimkan melalui email ke travineid@gmail.com atau WhatsApp Finance.
</div>

</body>
</html>
