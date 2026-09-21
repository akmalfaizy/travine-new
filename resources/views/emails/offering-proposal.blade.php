<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposal Penawaran Biaya Produksi</title>
</head>
<body style="margin:0; padding:0; background:#f0f4f8; font-family: Arial, sans-serif; -webkit-font-smoothing:antialiased;">
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
                    <div style="font-size:36px; text-align:center; margin-bottom:12px;">📄</div>
                    <h2 style="color:#0f2a52; margin:0; font-size:20px; text-align:center;">Proposal Penawaran Biaya Produksi</h2>
                    <p style="color:#64748b; margin:6px 0 20px; font-size:13px; text-align:center;">Nomor Dokumen: <strong style="color:#0f2a52;">{{ $offering->offering_number }}</strong></p>

                    <p style="color:#334155; font-size:14px; line-height:1.6; margin-bottom:16px;">
                        Kepada Yth. <strong>{{ $offering->pic_client ?: $offering->client_name }}</strong>,<br>
                        Terima kasih atas kepercayaan Anda kepada <strong>travine.id</strong>. Bersama email ini kami lampirkan dokumen resmi penawaran biaya produksi (<em>Production Cost Proposal</em>) untuk kegiatan project Anda.
                    </p>

                    @if(!empty($emailMessage))
                    <div style="background:#f8fafc; border-left:4px solid #14B8A6; padding:12px 16px; margin-bottom:20px; border-radius:0 8px 8px 0; color:#1e293b; font-size:13px; line-height:1.5;">
                        <strong style="color:#0f2a52; display:block; margin-bottom:4px;">Pesan Khusus dari Tim Travine:</strong>
                        {!! nl2br(e($emailMessage)) !!}
                    </div>
                    @endif

                    {{-- Project Summary Table --}}
                    <table width="100%" cellpadding="10" cellspacing="0" style="background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0; font-size:13px; margin-bottom:20px;">
                        <tr>
                            <td colspan="2" style="border-bottom:1px solid #e2e8f0; font-weight:bold; color:#0f2a52; padding:12px 14px; background:#f1f5f9;">
                                Ringkasan Penawaran
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#64748b; width:160px; padding:10px 14px;">Nama Klien / Perusahaan</td>
                            <td style="font-weight:600; color:#1e293b; padding:10px 14px;">{{ $offering->client_name }}</td>
                        </tr>
                        <tr style="background:#ffffff;">
                            <td style="color:#64748b; padding:10px 14px;">Paket / Kegiatan</td>
                            <td style="font-weight:600; color:#1e293b; padding:10px 14px;">{{ $offering->package_name }}</td>
                        </tr>
                        <tr>
                            <td style="color:#64748b; padding:10px 14px;">Tanggal / Durasi</td>
                            <td style="color:#1e293b; padding:10px 14px;">{{ $offering->date_duration ?: '-' }}</td>
                        </tr>
                        <tr style="background:#ffffff;">
                            <td style="color:#64748b; padding:10px 14px;">Jumlah Peserta</td>
                            <td style="color:#1e293b; padding:10px 14px;">{{ number_format($offering->participants) }} Orang / Pax</td>
                        </tr>
                        <tr>
                            <td style="color:#64748b; padding:10px 14px;">Biaya Produksi (Subtotal)</td>
                            <td style="color:#1e293b; padding:10px 14px;">{{ $offering->production_cost_formatted }}</td>
                        </tr>
                        <tr style="background:#ffffff;">
                            <td style="color:#64748b; padding:10px 14px; font-weight:bold;">Grand Total Penawaran</td>
                            <td style="font-weight:bold; color:#0f2a52; font-size:16px; padding:10px 14px;">
                                {{ $offering->total_amount_formatted }}
                            </td>
                        </tr>
                        <tr style="background:#f0fdf4;">
                            <td style="color:#166534; padding:10px 14px; font-weight:bold;">Estimasi Biaya / Pax</td>
                            <td style="font-weight:bold; color:#15803d; font-size:14px; padding:10px 14px;">
                                {{ $offering->price_per_pack_formatted }} / Pax
                            </td>
                        </tr>
                    </table>

                    {{-- Attachment Info Callout --}}
                    <div style="background:#e6fffa; border:1px dashed #14B8A6; border-radius:8px; padding:14px 16px; text-align:center; margin-bottom:24px;">
                        <span style="font-size:16px; vertical-align:middle;">📎</span>
                        <span style="color:#0f766e; font-size:13px; font-weight:600; margin-left:6px;">
                            Dokumen PDF Proposal Penawaran resmi terlampir pada email ini.
                        </span>
                    </div>

                    {{-- Contact PIC Info --}}
                    @if(!empty($offering->pic_project))
                    <div style="background:#f8fafc; border-radius:8px; padding:14px; border:1px solid #e2e8f0; font-size:12.5px; color:#475569;">
                        <strong>PIC Project Travine:</strong> {{ $offering->pic_project }} 
                        @if($offering->pic_contact) | Telp/WA: <strong>{{ $offering->pic_contact }}</strong> @endif
                        <br>
                        Silakan hubungi kami apabila ada pertanyaan atau penyesuaian kebutuhan paket.
                    </div>
                    @endif
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
                        Dokumen penawaran ini bersifat rahasia dan diterbitkan khusus untuk pihak penerima.
                    </p>
                </td>
            </tr>
        </table>
    </td></tr>
</table>
</body>
</html>
