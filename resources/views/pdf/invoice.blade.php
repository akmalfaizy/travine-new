<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size:13px; color:#1a202c; background:#fff; }
    .header { background: linear-gradient(135deg, #0f2a52 0%, #14B8A6 100%); color:#fff; padding:28px 36px; display:flex; justify-content:space-between; align-items:center; }
    .logo-text { font-size:24px; font-weight:bold; letter-spacing:1px; }
    .logo-sub { font-size:11px; opacity:0.8; margin-top:2px; }
    .invoice-label { text-align:right; }
    .invoice-label h2 { font-size:20px; font-weight:bold; text-transform:uppercase; letter-spacing:2px; }
    .invoice-label p { font-size:11px; opacity:0.85; margin-top:4px; }
    .body { padding:30px 36px; }
    .info-grid { display:table; width:100%; margin-bottom:24px; }
    .info-col { display:table-cell; vertical-align:top; width:50%; }
    .info-col h4 { font-size:10px; text-transform:uppercase; letter-spacing:0.1em; color:#718096; margin-bottom:8px; border-bottom:1px solid #e2e8f0; padding-bottom:4px; }
    .info-col p { font-size:13px; margin-bottom:4px; color:#2d3748; }
    .info-col .name { font-size:15px; font-weight:bold; color:#0f2a52; }
    table.items { width:100%; border-collapse:collapse; margin-bottom:20px; }
    table.items th { background:#f7fafc; padding:10px 14px; text-align:left; font-size:11px; text-transform:uppercase; color:#718096; border-bottom:2px solid #e2e8f0; }
    table.items td { padding:12px 14px; border-bottom:1px solid #f0f4f8; vertical-align:top; }
    .total-row { background:#f7fafc; }
    .total-row td { font-weight:bold; font-size:14px; color:#0f2a52; padding:14px; border-top:2px solid #14B8A6; }
    .status-row { margin-bottom:20px; }
    .status-badge { display:inline-block; padding:6px 16px; border-radius:20px; font-size:12px; font-weight:bold; }
    .status-confirmed { background:#d1fae5; color:#065f46; }
    .status-waiting  { background:#fef3c7; color:#92400e; }
    .status-other    { background:#e0e7ff; color:#3730a3; }
    .qr-section { text-align:center; margin-top:24px; padding:16px; background:#f7fafc; border-radius:8px; border:1px dashed #cbd5e0; }
    .qr-section p { font-size:11px; color:#718096; margin-top:6px; }
    .footer { margin-top:30px; padding:16px 36px; background:#f7fafc; text-align:center; font-size:11px; color:#a0aec0; border-top:1px solid #e2e8f0; }
    .teal { color:#14B8A6; }
    .highlight { background:#ebfdf9; border-left:3px solid #14B8A6; padding:10px 14px; margin-bottom:20px; border-radius:4px; }
</style>
</head>
<body>
<div class="header" style="background: linear-gradient(135deg, #0f2a52 0%, #14B8A6 100%); padding: 20px 36px;">
    <table style="width: 100%; border: none; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; vertical-align: middle; border: none; padding: 0;">
                <img src="{{ public_path('images/logo_tagline.png') }}" style="height: 38px; width: auto; display: block;" alt="travine.id Logo">
            </td>
            <td style="width: 50%; text-align: right; vertical-align: middle; border: none; padding: 0; color: #ffffff;">
                <h2 style="font-size: 20px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; color: #ffffff; margin: 0; padding: 0; line-height: 1;">Invoice</h2>
                <p style="font-size: 11px; opacity: 0.85; margin-top: 4px; color: #ffffff; margin-bottom: 0; padding: 0; line-height: 1;">{{ $booking->invoice_number ?? $booking->booking_code }}</p>
            </td>
        </tr>
    </table>
</div>

<div class="body">
    <div class="highlight">
        <strong>Kode Booking:</strong> {{ $booking->booking_code }} &nbsp;|&nbsp;
        <strong>Tanggal:</strong> {{ $booking->created_at->format('d M Y') }} &nbsp;|&nbsp;
        <strong>Keberangkatan:</strong> {{ $booking->departure_date->format('d M Y') }}
    </div>

    <div class="info-grid">
        <div class="info-col">
            <h4>Informasi Pemesan</h4>
            <p class="name">{{ $booking->name }}</p>
            <p><strong>No. HP:</strong> {{ $booking->whatsapp }}</p>
            <p><strong>Email:</strong> {{ $booking->email }}</p>
        </div>
        <div class="info-col" style="text-align:right;">
            <h4>Status</h4>
            @php
                $sc = match($booking->booking_status) {
                    'confirmed','completed' => 'confirmed',
                    'waiting_payment','waiting_verification','processing' => 'waiting',
                    default => 'other'
                };
            @endphp
            <span class="status-badge status-{{ $sc }}">{{ $booking->booking_status_label }}</span>
            <br><br>
            <p><strong>Pembayaran:</strong> {{ $booking->payment_status_label }}</p>
        </div>
    </div>

    @php
        $isTransport = $booking->tour?->category?->slug === 'transportasi';
        $unitPrice = $booking->tour?->price ?? 0;
        
        if ($isTransport) {
            $quantity = $booking->duration_days;
            $quantityLabel = 'Durasi';
            $quantityValue = $quantity . ' hari';
            $priceLabel = 'Harga/Hari';
            $itemSubtotal = $unitPrice * $quantity;
        } else {
            $quantity = $booking->participants;
            $quantityLabel = 'Peserta';
            $quantityValue = $quantity . ' orang';
            $priceLabel = 'Harga/Orang';
            $itemSubtotal = $unitPrice * $quantity;
        }
        
        $footerSubtotal = $booking->total_price + $booking->discount;
    @endphp

    <table class="items">
        <thead>
            <tr>
                <th>Paket {{ $isTransport ? 'Sewa' : 'Wisata' }}</th>
                <th>Lokasi</th>
                @if(!$isTransport)
                    <th>Durasi</th>
                @endif
                <th>{{ $quantityLabel }}</th>
                <th>{{ $priceLabel }}</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>{{ $booking->tour?->title }}</strong></td>
                <td>{{ $booking->tour?->location }}</td>
                @if(!$isTransport)
                    <td>{{ $booking->duration ?? $booking->tour?->duration }}</td>
                @endif
                <td style="text-align:center;">{{ $quantityValue }}</td>
                <td>Rp {{ number_format($unitPrice, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($itemSubtotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="{{ $isTransport ? 4 : 5 }}" style="text-align:right; border:none; padding:6px 14px; font-size:12px; color:#718096;">Subtotal</td>
                <td style="border:none; padding:6px 14px; font-size:12px; color:#4a5568;">Rp {{ number_format($footerSubtotal, 0, ',', '.') }}</td>
            </tr>
            @if($booking->discount > 0)
                <tr>
                    <td colspan="{{ $isTransport ? 4 : 5 }}" style="text-align:right; border:none; padding:6px 14px; font-size:12px; color:#ef4444;">Diskon</td>
                    <td style="border:none; padding:6px 14px; font-size:12px; color:#ef4444;">-{{ $booking->discount_formatted }}</td>
                </tr>
            @endif
            <tr class="total-row">
                <td colspan="{{ $isTransport ? 4 : 5 }}" style="text-align:right;">Total Biaya</td>
                <td class="teal">{{ $booking->total_price_formatted }}</td>
            </tr>
            @if($booking->payment_status === 'paid')
                <tr>
                    <td colspan="{{ $isTransport ? 4 : 5 }}" style="text-align:right; border:none; padding:6px 14px; font-size:12px; font-weight:bold; color:#059669;">Telah Dibayar Lunas</td>
                    <td style="border:none; padding:6px 14px; font-size:12px; font-weight:bold; color:#059669;">{{ $booking->total_price_formatted }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="{{ $isTransport ? 4 : 5 }}" style="text-align:right; border:none; padding:6px 14px; font-size:12px; font-weight:bold; color:#f97316;">Uang Muka (DP) Dibayar</td>
                    <td style="border:none; padding:6px 14px; font-size:12px; font-weight:bold; color:#f97316;">{{ $booking->paid_amount_formatted }}</td>
                </tr>
                <tr>
                    <td colspan="{{ $isTransport ? 4 : 5 }}" style="text-align:right; border:none; padding:6px 14px; font-size:12px; font-weight:bold; color:#0f2a52;">Sisa Tagihan Pelunasan (H-1)</td>
                    <td style="border:none; padding:6px 14px; font-size:12px; font-weight:bold; color:#0f2a52;">Rp {{ number_format($booking->remaining_amount, 0, ',', '.') }}</td>
                </tr>
            @endif
        </tfoot>
    </table>

    @if(!empty($booking->participants_data) && is_array($booking->participants_data))
    <div style="margin-top:16px;">
        <h4 style="font-size:11px; font-weight:bold; color:#0f2a52; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px;">Daftar Peserta Wisata (Asuransi Perjalanan)</h4>
        <table class="items" style="margin-top:0;">
            <thead>
                <tr>
                    <th style="width:25px; text-align:center;">#</th>
                    <th>Nama Peserta</th>
                    <th>NIK / Identitas</th>
                    <th>Tgl Lahir</th>
                    <th>No. Telepon</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->participants_data as $idx => $p)
                <tr>
                    <td style="text-align:center; font-size:11px;">{{ $idx + 1 }}</td>
                    <td style="font-size:11px;">
                        <strong>{{ $p['name'] ?? '-' }}</strong>
                        @if($idx === 0)
                            <span style="font-size:9px; color:#14b8a6; font-weight:bold;">(PIC)</span>
                        @endif
                    </td>
                    <td style="font-size:11px;">{{ $p['nik'] ?? '-' }}</td>
                    <td style="font-size:11px;">{{ !empty($p['birth_date']) ? \Carbon\Carbon::parse($p['birth_date'])->format('d/m/Y') : '-' }}</td>
                    <td style="font-size:11px;">{{ $p['phone'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="qr-section">
        <img src="data:image/png;base64,{{ $qrBase64 }}" width="120" height="120">
        <p>Scan QR code untuk verifikasi keaslian dokumen ini</p>
    </div>
</div>

<div class="footer">
    travine.id — Jelajahi Keindahan Nusantara | travineid@gmail.com<br>
    Dokumen ini dibuat secara otomatis dan sah tanpa tanda tangan basah.
</div>
</body>
</html>
