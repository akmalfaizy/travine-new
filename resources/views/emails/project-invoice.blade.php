<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Project - {{ $projectInvoice->invoice_number }}</title>
</head>
<body style="margin:0; padding:0; background:#f0f4f8; font-family: Arial, sans-serif; -webkit-font-smoothing:antialiased;">
@php
    $status = $projectInvoice->payment_status;
    $statusTitle = match($status) {
        'paid'      => 'Pembayaran LUNAS Diterima',
        'partial'   => 'Konfirmasi Pembayaran Uang Muka (DP)',
        'overdue'   => 'Pemberitahuan Jatuh Tempo Invoice',
        'cancelled' => 'Pemberitahuan Invoice Dibatalkan',
        default     => 'Tagihan Invoice Resmi Project',
    };
    $statusBadgeBg = match($status) {
        'paid'      => '#d1fae5',
        'partial'   => '#fef3c7',
        'overdue'   => '#fee2e2',
        'cancelled' => '#f1f5f9',
        default     => '#e0f2fe',
    };
    $statusBadgeColor = match($status) {
        'paid'      => '#065f46',
        'partial'   => '#92400e',
        'overdue'   => '#991b1b',
        'cancelled' => '#64748b',
        default     => '#0369a1',
    };
    $statusIcon = match($status) {
        'paid'      => '🎉',
        'partial'   => '💳',
        'overdue'   => '⚠️',
        'cancelled' => '❌',
        default     => '📑',
    };
@endphp
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8; padding:32px 0;">
    <tr><td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.08); max-width:600px;">
            {{-- Header --}}
            <tr>
                <td style="background:linear-gradient(135deg, #0f2a52 0%, #14B8A6 100%); padding:32px; text-align:center;">
                    <h1 style="color:#ffffff; font-size:26px; margin:0; font-weight:bold; letter-spacing:0.5px;">Travine<span style="color:#5eead4;">.id</span></h1>
                    <p style="color:rgba(255,255,255,0.85); margin:6px 0 0; font-size:13px;">Corporate Travel & Project Production Partner</p>
                </td>
            </tr>

            {{-- Main Content --}}
            <tr>
                <td style="padding:32px;">
                    <div style="font-size:36px; text-align:center; margin-bottom:12px;">{{ $statusIcon }}</div>
                    <h2 style="color:#0f2a52; margin:0; font-size:20px; text-align:center;">{{ $statusTitle }}</h2>
                    <p style="color:#64748b; margin:6px 0 12px; font-size:13px; text-align:center;">Nomor Invoice: <strong style="color:#0f2a52;">{{ $projectInvoice->invoice_number }}</strong></p>
                    <div style="text-align:center; margin-bottom:20px;">
                        <span style="display:inline-block; padding:4px 14px; border-radius:12px; background:{{ $statusBadgeBg }}; color:{{ $statusBadgeColor }}; font-weight:bold; font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">
                            STATUS: {{ strtoupper($status) }}
                        </span>
                    </div>

                    <p style="color:#334155; font-size:14px; line-height:1.6; margin-bottom:16px;">
                        Kepada Yth. <strong>{{ $projectInvoice->client_company ?: $projectInvoice->client_name }}</strong><br>
                        @if($projectInvoice->pic_client) (Up. {{ $projectInvoice->pic_client }})<br> @endif
                        
                        @if($status === 'paid')
                            Terima kasih! Kami mengonfirmasi bahwa pembayaran invoice Anda telah kami terima <strong>LUNAS</strong>. Dokumen kuitansi resmi terlampir pada email ini.
                        @elseif($status === 'partial')
                            Terima kasih! Pembayaran sebagian (DP) sebesar <strong>{{ $projectInvoice->amount_paid_formatted }}</strong> telah kami terima. Berikut adalah rincian sisa tagihan yang perlu diselesaikan.
                        @else
                            Bersama email ini kami lampirkan dokumen resmi tagihan invoice project <strong>{{ $projectInvoice->project_name }}</strong> yang jatuh tempo pada <strong>{{ $projectInvoice->due_date ? $projectInvoice->due_date->format('d M Y') : '-' }}</strong>.
                        @endif
                    </p>

                    @if(!empty($emailMessage))
                    <div style="background:#f8fafc; border-left:4px solid #14B8A6; padding:12px 16px; margin-bottom:20px; border-radius:0 8px 8px 0; color:#1e293b; font-size:13px; line-height:1.5;">
                        <strong style="color:#0f2a52; display:block; margin-bottom:4px;">Catatan Finance Travine:</strong>
                        {!! nl2br(e($emailMessage)) !!}
                    </div>
                    @endif

                    {{-- Invoice Financial Summary --}}
                    <table width="100%" cellpadding="10" cellspacing="0" style="background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; margin-bottom:20px;">
                        <tr>
                            <td colspan="2" style="border-bottom:1px solid #e2e8f0; font-weight:bold; color:#0f2a52; padding:12px 14px; background:#f1f5f9;">
                                Rincian Tagihan Invoice
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#64748b; width:160px; padding:10px 14px;">Nama Project</td>
                            <td style="font-weight:600; color:#1e293b; padding:10px 14px;">{{ $projectInvoice->project_name }}</td>
                        </tr>
                        <tr style="background:#ffffff;">
                            <td style="color:#64748b; padding:10px 14px;">Tanggal Terbit</td>
                            <td style="color:#1e293b; padding:10px 14px;">{{ $projectInvoice->issue_date ? $projectInvoice->issue_date->format('d M Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td style="color:#64748b; padding:10px 14px;">Jatuh Tempo</td>
                            <td style="color:#1e293b; padding:10px 14px; font-weight:600;">{{ $projectInvoice->due_date ? $projectInvoice->due_date->format('d M Y') : '-' }}</td>
                        </tr>
                        <tr style="background:#ffffff;">
                            <td style="color:#64748b; padding:10px 14px;">Total Tagihan Kotor (Gross)</td>
                            <td style="color:#1e293b; padding:10px 14px; font-weight:600;">{{ $projectInvoice->gross_total_formatted }}</td>
                        </tr>
                        @if($projectInvoice->include_pph_deduction && $projectInvoice->pph_amount > 0)
                        <tr>
                            <td style="color:#b91c1c; padding:10px 14px;">Potongan PPh ({{ number_format($projectInvoice->pph_percentage, 1) }}%)</td>
                            <td style="color:#b91c1c; padding:10px 14px; font-weight:600;">-{{ $projectInvoice->pph_amount_formatted }}</td>
                        </tr>
                        <tr style="background:#ffffff;">
                            <td style="color:#64748b; padding:10px 14px; font-weight:bold;">Total Bersih (Net Amount)</td>
                            <td style="color:#0f2a52; padding:10px 14px; font-weight:bold;">{{ $projectInvoice->net_amount_formatted }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td style="color:#15803d; padding:10px 14px; font-weight:bold;">Jumlah Sudah Dibayar</td>
                            <td style="color:#15803d; padding:10px 14px; font-weight:bold; font-size:14px;">
                                {{ $projectInvoice->amount_paid_formatted }}
                            </td>
                        </tr>
                        @if($projectInvoice->payment_status === 'paid' || $projectInvoice->remaining_balance <= 0)
                        <tr style="background:#f0fdf4;">
                            <td style="color:#166534; padding:12px 14px; font-weight:bold;">SISA TAGIHAN</td>
                            <td style="color:#166534; padding:12px 14px; font-weight:bold; font-size:16px;">
                                Rp 0 (LUNAS)
                            </td>
                        </tr>
                        @else
                        <tr style="background:#fef2f2;">
                            <td style="color:#b91c1c; padding:12px 14px; font-weight:bold;">SISA TAGIHAN PELUNASAN</td>
                            <td style="color:#b91c1c; padding:12px 14px; font-weight:bold; font-size:16px;">
                                {{ $projectInvoice->remaining_balance_formatted }}
                            </td>
                        </tr>
                        @endif
                    </table>

                    {{-- Bank Information if not paid --}}
                    @if($projectInvoice->payment_status !== 'paid' && $projectInvoice->remaining_balance > 0)
                    <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:16px; margin-bottom:20px;">
                        <strong style="color:#166534; font-size:13px; display:block; margin-bottom:8px;">Informasi Rekening Pembayaran:</strong>
                        <table width="100%" cellpadding="3" cellspacing="0" style="font-size:12.5px; color:#14532d;">
                            <tr>
                                <td style="width:110px; color:#166534;">Bank</td>
                                <td>: <strong>{{ $projectInvoice->bank_name ?: 'BCA (Bank Central Asia)' }}</strong></td>
                            </tr>
                            <tr>
                                <td style="color:#166534;">Nomor Rekening</td>
                                <td>: <strong style="font-size:14px; color:#0f2a52;">{{ $projectInvoice->bank_account ?: '842-089-1234' }}</strong></td>
                            </tr>
                            <tr>
                                <td style="color:#166534;">Atas Nama</td>
                                <td>: <strong>{{ $projectInvoice->bank_holder ?: 'PT Travine Nusantara' }}</strong></td>
                            </tr>
                        </table>
                        <div style="font-size:11.5px; color:#166534; margin-top:8px; line-height:1.4;">
                            * Mohon sertakan nomor invoice (<strong>{{ $projectInvoice->invoice_number }}</strong>) pada berita transfer dan kirimkan bukti transfer kepada kami.
                        </div>
                    </div>
                    @endif

                    {{-- Attachment Info Callout --}}
                    <div style="background:#e6fffa; border:1px dashed #14B8A6; border-radius:8px; padding:14px 16px; text-align:center; margin-bottom:24px;">
                        <span style="font-size:16px; vertical-align:middle;">📎</span>
                        <span style="color:#0f766e; font-size:13px; font-weight:600; margin-left:6px;">
                            Dokumen PDF Invoice resmi terlampir pada email ini.
                        </span>
                    </div>

                    {{-- Footer Notice --}}
                    <div style="background:#f8fafc; border-radius:8px; padding:12px 14px; border:1px solid #e2e8f0; font-size:12px; color:#64748b;">
                        Ada pertanyaan mengenai tagihan ini? Hubungi Tim Finance kami via WhatsApp atau balas email ini.
                    </div>
                </td>
            </tr>

            {{-- Footer --}}
            <tr>
                <td style="background:#f8fafc; padding:20px 32px; text-align:center; border-top:1px solid #e2e8f0;">
                    <p style="color:#94a3b8; font-size:12px; margin:0 0 6px;">
                        <strong>travine.id</strong> (PT Travine Nusantara) — Jelajahi Keindahan Nusantara
                    </p>
                    <p style="color:#94a3b8; font-size:11px; margin:0;">
                        Email: travineid@gmail.com | Website: travine.id<br>
                        Invoice ini diterbitkan secara sah oleh sistem manajemen Travine.id.
                    </p>
                </td>
            </tr>
        </table>
    </td></tr>
</table>
</body>
</html>
