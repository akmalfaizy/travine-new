<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manifest Peserta Open Trip - {{ $selectedTour ? $selectedTour->title : 'Travine' }}</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8.5px;
            color: #1e293b;
            background: #ffffff;
            line-height: 1.35;
            padding: 10px 15px;
        }

        /* Top Header */
        .top-header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            border-bottom: 2px solid #0f2a52;
            padding-bottom: 10px;
        }
        .top-header td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .logo-img {
            height: 38px;
            width: auto;
            display: block;
        }
        .company-title {
            font-size: 8px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-top: 4px;
        }
        .doc-title {
            text-align: right;
        }
        .doc-title h1 {
            font-size: 13.5px;
            font-weight: 800;
            color: #0f2a52;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .doc-title p {
            font-size: 8px;
            color: #64748b;
        }

        /* Info Box */
        .info-card {
            width: 100%;
            border-collapse: collapse;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            margin-bottom: 12px;
        }
        .info-card td {
            padding: 8px 12px;
            vertical-align: top;
            border: none;
            width: 33.33%;
        }
        .info-label {
            font-size: 7.2px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .info-value {
            font-size: 9.5px;
            font-weight: bold;
            color: #0f2a52;
            word-break: break-word;
        }
        .info-sub {
            font-size: 8px;
            color: #475569;
            margin-top: 2px;
            word-break: break-word;
        }

        /* Stat Bar */
        .stat-bar {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .stat-bar td {
            padding: 7px 10px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            vertical-align: middle;
            text-align: center;
            width: 25%;
        }
        .stat-num {
            font-size: 12px;
            font-weight: 800;
            color: #0f2a52;
        }
        .stat-desc {
            font-size: 7.2px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            margin-top: 1px;
        }

        /* Manifest Table */
        table.manifest-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        table.manifest-table thead {
            display: table-header-group;
        }
        table.manifest-table tr {
            page-break-inside: avoid;
        }
        table.manifest-table th {
            background: #0f2a52;
            color: #ffffff;
            font-size: 7.8px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 7px 5px;
            border: 1px solid #0f2a52;
            vertical-align: middle;
            text-align: left;
        }
        table.manifest-table td {
            padding: 6px 5px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
            font-size: 8px;
            line-height: 1.35;
            word-break: break-word;
            overflow-wrap: break-word;
        }
        table.manifest-table tr:nth-child(even) td {
            background: #f8fafc;
        }

        /* Status Badges */
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7.2px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }
        .badge-paid {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .badge-dp {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }
        .badge-unpaid {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        /* Footer & Signatures */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            page-break-inside: avoid;
        }
        .footer-table td {
            vertical-align: top;
            border: none;
            padding: 0;
        }
        .notes-box {
            background: #f1f5f9;
            border-left: 3px solid #14b8a6;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 7.5px;
            color: #334155;
            line-height: 1.4;
            max-width: 320px;
        }
        .notes-box strong {
            color: #0f2a52;
            text-transform: uppercase;
            display: block;
            margin-bottom: 2px;
        }
        .sign-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        .sign-table td {
            width: 50%;
            padding: 0 6px;
            vertical-align: top;
        }
        .sign-title {
            font-size: 7.5px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            margin-bottom: 42px;
        }
        .sign-line {
            border-top: 1px dashed #64748b;
            padding-top: 2px;
            font-size: 8px;
            font-weight: bold;
            color: #0f2a52;
        }
        .sign-role {
            font-size: 7px;
            color: #64748b;
        }
    </style>
</head>
<body>

    {{-- 1. Top Header with Logo --}}
    <table class="top-header">
        <tr>
            <td style="width: 48%; vertical-align: middle;">
                @if($logoBase64)
                    <img src="data:image/png;base64,{{ $logoBase64 }}" class="logo-img" alt="Travine Logo">
                @else
                    <div style="font-size: 18px; font-weight: 800; color: #0f2a52; letter-spacing: -0.5px;">
                        TRAVINE<span style="color:#14b8a6;">.ID</span>
                    </div>
                @endif
                <div class="company-title">PT Travine Wisata Nusantara &bull; Operasional Lapangan</div>
            </td>
            <td class="doc-title" style="width: 52%; vertical-align: middle;">
                <h1>MANIFEST PESERTA OPEN TRIP</h1>
                <p>Dokumen Resmi Daftar Penumpang &amp; Kehadiran Peserta Tour Leader (TL)</p>
                <p style="margin-top: 2px;">Cetak: <strong>{{ now()->translatedFormat('d F Y, H:i') }} WIB</strong></p>
            </td>
        </tr>
    </table>

    {{-- 2. Informasi Trip & Filter Card --}}
    <table class="info-card">
        <tr>
            <td>
                <div class="info-label">Paket Wisata / Open Trip</div>
                <div class="info-value">
                    {{ $selectedTour ? $selectedTour->title : 'Seluruh Paket Open Trip (Gabungan)' }}
                </div>
                <div class="info-sub">
                    Lokasi: <strong>{{ $selectedTour ? ($selectedTour->location ?: 'Indonesia') : 'Berbagai Destinasi' }}</strong>
                    @if($selectedTour && $selectedTour->duration)
                        &bull; Durasi: <strong>{{ $selectedTour->duration }}</strong>
                    @endif
                </div>
            </td>
            <td>
                <div class="info-label">Jadwal Keberangkatan</div>
                <div class="info-value" style="color: #0284c7;">
                    @if($departureDate)
                        📅 {{ $departureDate->translatedFormat('l, d F Y') }}
                    @elseif($bookings->count() > 0 && $bookings->pluck('departure_date')->unique()->count() === 1)
                        📅 {{ $bookings->first()->departure_date->translatedFormat('l, d F Y') }}
                    @else
                        📅 Sesuai Jadwal Masing-Masing Pemesan
                    @endif
                </div>
                <div class="info-sub">
                    Format: <strong>Kertas HVS (A4 Portrait)</strong>
                </div>
            </td>
            <td>
                <div class="info-label">Ringkasan Peserta</div>
                <div class="info-value" style="color: #15803d;">
                    {{ $totalPax }} Orang Peserta (Pax)
                </div>
                <div class="info-sub">
                    Total Pesanan: <strong>{{ $totalBookings }} Kelompok</strong>
                </div>
            </td>
        </tr>
    </table>

    {{-- 3. Baris Statistik Jumlah Peserta (Bersih tanpa nominal Rupiah) --}}
    <table class="stat-bar">
        <tr>
            <td>
                <div class="stat-num">{{ $totalPax }} Pax</div>
                <div class="stat-desc">Total Peserta</div>
            </td>
            <td>
                <div class="stat-num" style="color: #15803d;">{{ $totalPaidPax }} Pax</div>
                <div class="stat-desc">Status Lunas</div>
            </td>
            <td>
                <div class="stat-num" style="color: #b45309;">{{ $totalDpPax }} Pax</div>
                <div class="stat-desc">Status DP</div>
            </td>
            <td>
                <div class="stat-num" style="color: #b91c1c;">{{ $totalUnpaidPax }} Pax</div>
                <div class="stat-desc">Belum Bayar</div>
            </td>
        </tr>
    </table>

    {{-- 4. Tabel Manifest Data Peserta --}}
    <table class="manifest-table">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 13%;">Kode / Tgl</th>
                <th style="width: 21%;">Pemesan (PIC)</th>
                <th style="width: 30%;">Daftar Nama Peserta (Pax)</th>
                <th style="width: 6%; text-align: center;">Pax</th>
                <th style="width: 11%; text-align: center;">Status</th>
                <th style="width: 15%;">Catatan / Titik Kumpul</th>
                <th style="width: 9%; text-align: center;">Paraf</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $idx => $b)
                @php
                    $participants = $b->participants_data ?? [];
                    $isPaid = $b->payment_status === 'paid';
                    $isDp   = $b->payment_status === 'dp_paid';
                @endphp
                <tr>
                    {{-- No --}}
                    <td style="text-align: center; font-weight: bold; color: #475569;">
                        {{ $idx + 1 }}
                    </td>

                    {{-- Kode & Tgl Berangkat --}}
                    <td>
                        <div style="font-weight: 800; font-family: monospace; font-size: 8.5px; color: #0f2a52;">
                            {{ $b->booking_code }}
                        </div>
                        <div style="font-size: 7.2px; color: #64748b; margin-top: 1px;">
                            {{ $b->departure_date ? $b->departure_date->format('d/m/Y') : '-' }}
                        </div>
                        @if(!$selectedTour && $b->tour)
                            <div style="font-size: 7px; color: #0284c7; margin-top: 1px;">
                                {{ $b->tour->title }}
                            </div>
                        @endif
                    </td>

                    {{-- Pemesan / PIC Kontak (Lengkap tanpa ...) --}}
                    <td>
                        <div style="font-weight: bold; color: #0f2a52; font-size: 8.5px;">
                            {{ $b->name }}
                        </div>
                        <div style="font-size: 7.8px; color: #14b8a6; font-weight: 600; margin-top: 1px;">
                            📱 {{ $b->whatsapp }}
                        </div>
                        @if($b->email)
                            <div style="font-size: 7px; color: #64748b; margin-top: 1px;">
                                {{ $b->email }}
                            </div>
                        @endif
                    </td>

                    {{-- Rincian Nama Peserta (Lengkap tanpa ...) --}}
                    <td>
                        @if(!empty($participants) && is_array($participants))
                            <table style="width: 100%; border: none; border-collapse: collapse; background: transparent;">
                                @foreach($participants as $pIdx => $p)
                                    <tr>
                                        <td style="padding: 1px 0; border: none; font-size: 7.8px; background: transparent;">
                                            <span style="font-weight: bold; color: #0f2a52;">{{ $pIdx + 1 }}.</span>
                                            <strong>{{ $p['name'] ?? '-' }}</strong>
                                            @if(!empty($p['phone']) && $p['phone'] !== $b->whatsapp)
                                                <span style="color: #64748b; font-size: 7.2px;">({{ $p['phone'] }})</span>
                                            @endif
                                            @if(!empty($p['nik']))
                                                <div style="color: #64748b; font-size: 6.8px; font-family: monospace;">NIK: {{ $p['nik'] }}</div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        @else
                            {{-- Jika data perorangan belum dipecah --}}
                            <div style="font-size: 7.8px; color: #334155;">
                                <strong>1. {{ $b->name }}</strong> (PIC)
                                @if($b->participants > 1)
                                    <div style="font-size: 7.2px; color: #64748b; font-style: italic;">
                                        + {{ $b->participants - 1 }} Rekan Rombongan
                                    </div>
                                @endif
                            </div>
                        @endif
                    </td>

                    {{-- Pax --}}
                    <td style="text-align: center; vertical-align: middle;">
                        <span style="font-size: 10px; font-weight: 800; color: #0f2a52;">
                            {{ $b->participants }}
                        </span>
                        <div style="font-size: 6.5px; color: #64748b;">Pax</div>
                    </td>

                    {{-- Status Pembayaran (Tanpa Angka Rupiah) --}}
                    <td style="text-align: center; vertical-align: middle;">
                        @if($isPaid)
                            <span class="badge badge-paid">✓ LUNAS</span>
                        @elseif($isDp)
                            <span class="badge badge-dp">⚡ DP</span>
                        @else
                            <span class="badge badge-unpaid">BELUM</span>
                        @endif
                    </td>

                    {{-- Meeting Point / Catatan (Lengkap tanpa ...) --}}
                    <td>
                        @if($b->notes)
                            <div style="font-size: 7.5px; color: #334155;">
                                {{ $b->notes }}
                            </div>
                        @else
                            <span style="color: #94a3b8; font-size: 7px;">- Titik kumpul standar</span>
                        @endif
                    </td>

                    {{-- Paraf Lapangan (Polosan Kosong) --}}
                    <td style="vertical-align: middle; text-align: center; height: 26px;">
                        &nbsp;
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 24px; color: #64748b; font-style: italic;">
                        Tidak ada data pendaftar open trip untuk kriteria filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 5. Footer: SOP Lapangan & Tanda Tangan --}}
    <table class="footer-table">
        <tr>
            <td style="width: 55%;">
                <div class="notes-box">
                    <strong>Petunjuk Operasional Tour Leader (TL):</strong>
                    1. Pastikan setiap peserta/rombongan membubuhkan <strong>paraf</strong> di kolom paraf saat registrasi di meeting point.<br>
                    2. Untuk peserta berstatus <strong>DP</strong>, koordinasikan konfirmasi pelunasan sebelum trip dimulai.<br>
                    3. Berikan pengarahan keselamatan dan bagikan kontak darurat kepada seluruh peserta.
                </div>
            </td>
            <td style="width: 45%;">
                <table class="sign-table">
                    <tr>
                        <td>
                            <div class="sign-title">Tour Leader / Koordinator</div>
                            <div class="sign-line">( ............................................ )</div>
                            <div class="sign-role">Petugas Lapangan</div>
                        </td>
                        <td>
                            <div class="sign-title">Admin Operasional</div>
                            <div class="sign-line">( ............................................ )</div>
                            <div class="sign-role">Travine.id</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
