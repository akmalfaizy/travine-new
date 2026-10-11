<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--[if gte mso 9]>
    @verbatim
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Data Asuransi</x:Name>
                    <x:WorksheetOptions>
                        <!-- <x:DisplayGridlines/> -->
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    @endverbatim
    <![endif]-->
    <style>
        body {
            font-family: Calibri, Arial, sans-serif;
            font-size: 11pt;
            color: #1e293b;
        }
        .main-title {
            font-size: 16pt;
            font-weight: bold;
            color: #0f766e;
            text-align: left;
        }
        .sub-title {
            font-size: 11pt;
            color: #475569;
            margin-bottom: 15px;
        }
        .info-table td {
            font-size: 10pt;
            padding: 3px 6px;
        }
        .info-label {
            font-weight: bold;
            color: #334155;
            width: 180px;
        }
        .table-data {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
        }
        .table-data th {
            background-color: #0d9488;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            border: 1px solid #115e59;
            padding: 8px 10px;
            font-size: 10pt;
        }
        .table-data td {
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            font-size: 10pt;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        /* Format agar NIK dan Telepon dibaca teks oleh Excel (tanpa notasi ilmiah 3.2E+15 & menjaga angka 0 di depan) */
        .cell-text {
            mso-number-format: "\@";
            text-align: left;
        }
        .cell-nik {
            mso-number-format: "\@";
            text-align: center;
            font-family: Consolas, Arial, sans-serif;
        }
        .cell-phone {
            mso-number-format: "\@";
            text-align: left;
        }
        .row-even { background-color: #f8fafc; }
        .row-odd { background-color: #ffffff; }
        .summary-row td {
            background-color: #ccfbf1;
            font-weight: bold;
            border-top: 2px solid #0d9488;
            color: #0f766e;
        }
    </style>
</head>
<body>

    {{-- Header Dokumen --}}
    <table>
        <tr>
            <td colspan="7" class="main-title">DATA PESERTA ASURANSI PERJALANAN WISATA</td>
        </tr>
        <tr>
            <td colspan="7" class="sub-title">PT TRAVINE WISATA NUSANTARA - SISTEM MANIFEST OPERASIONAL</td>
        </tr>
        <tr><td colspan="7"></td></tr>
    </table>

    {{-- Metadata Filter --}}
    <table class="info-table">
        <tr>
            <td class="info-label">Paket Wisata</td>
            <td>: {{ $selectedTour ? $selectedTour->title : 'Semua Paket Wisata' }}</td>
        </tr>
        <tr>
            <td class="info-label">Tgl Keberangkatan</td>
            <td>: {{ $departureDateFormatted }}</td>
        </tr>
        <tr>
            <td class="info-label">Total Peserta</td>
            <td>: <strong>{{ count($rows) }} Orang</strong></td>
        </tr>
        <tr>
            <td class="info-label">Waktu Export</td>
            <td>: {{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
    </table>

    <br />

    {{-- Tabel Utama Data Peserta Asuransi --}}
    <table class="table-data" border="1">
        <thead>
            <tr>
                <th style="width: 45px;">No</th>
                <th style="width: 220px;">Nama</th>
                <th style="width: 190px;">Nomor Identitas (NIK/No. KTP/KITAS)</th>
                <th style="width: 120px;">Tgl Lahir</th>
                <th style="width: 320px;">Alamat</th>
                <th style="width: 150px;">Nomor Telepon</th>
                <th style="width: 200px;">Paket Wisata</th>
                <th style="width: 100px;">Kode Booking</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $r)
                <tr class="{{ $loop->iteration % 2 === 0 ? 'row-even' : 'row-odd' }}">
                    <td class="text-center" style="font-weight: bold;">{{ $r['no'] }}</td>
                    <td class="text-left">
                        <strong style="color: #0f172a;">{{ $r['name'] }}</strong>
                        @if(!empty($r['is_pic']))
                            <span style="font-size: 8pt; color: #0d9488;">(PIC)</span>
                        @endif
                    </td>
                    <td class="cell-nik">{{ $r['nik'] }}</td>
                    <td class="text-center">{{ $r['birth_date'] }}</td>
                    <td class="text-left cell-text">{{ $r['address'] }}</td>
                    <td class="cell-phone">{{ $r['phone'] }}</td>
                    <td class="text-left cell-text">{{ $r['tour_title'] }}</td>
                    <td class="text-center cell-text" style="font-weight: 600;">{{ $r['booking_code'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada data peserta untuk filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($rows) > 0)
        <tfoot>
            <tr class="summary-row">
                <td colspan="2" class="text-center">TOTAL PESERTA</td>
                <td colspan="6" class="text-left">{{ count($rows) }} Orang Peserta Terdaftar</td>
            </tr>
        </tfoot>
        @endif
    </table>

</body>
</html>
