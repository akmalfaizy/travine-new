@extends('admin.layout')

@section('title', 'Buat Invoice Manual')

@section('content')
<div class="admin-topbar">
    <h1>Buat Invoice Manual</h1>
    <a href="{{ route('admin.bookings.manualInvoiceList') }}" class="admin-btn admin-btn--secondary">← Kembali ke Daftar</a>
</div>

<div class="admin-card">
    <div class="admin-grid admin-grid--2" style="gap:24px;">
        <div>
            <h3>Detail Pemesanan</h3>
            <p><strong>Kode:</strong> {{ $booking->booking_code }}</p>
            <p><strong>Nama:</strong> {{ $booking->name }}</p>
            <p><strong>Paket Wisata:</strong> {{ $booking->tour?->title ?? '—' }}</p>
            <p><strong>Tanggal Berangkat:</strong> {{ $booking->departure_date->format('d M Y') }}</p>
            <p><strong>Total:</strong> {{ $booking->total_price_formatted }}</p>
        </div>
        <div>
            <form method="POST" action="{{ route('admin.bookings.manualInvoiceStore', $booking) }}">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="invoice_category">Kategori Invoice</label>
                    <select name="invoice_category" id="invoice_category" class="form-control" required>
                        <option value="" disabled selected> pilih kategori </option>
                        <option value="wisata">Wisata</option>
                        <option value="transport">Sewa Transportasi</option>
                        <option value="akomodasi">Akomodasi</option>
                    </select>
                </div>
                <button type="submit" class="admin-btn admin-btn--primary">Buat Invoice</button>
                <a href="{{ route('admin.bookings.manualInvoiceList') }}" class="admin-btn admin-btn--secondary" style="margin-left:8px;">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
