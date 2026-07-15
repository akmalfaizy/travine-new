@extends('admin.layout')

@section('title', 'Buat Invoice Manual')

@section('content')
<div class="admin-topbar">
    <h1>Buat Invoice Manual</h1>
    <a href="{{ route('admin.bookings.manualInvoiceList') }}" class="admin-btn admin-btn--secondary">← Kembali ke Daftar</a>
</div>

<div class="admin-card">
    <form method="POST" action="{{ route('admin.bookings.manualInvoiceCreate') }}">
        @csrf
        <div class="form-group">
            <label class="form-label" for="booking_id">Pilih Booking</label>
            <select name="booking_id" id="booking_id" class="form-control" required>
                <option value="" disabled selected>-- Pilih Booking --</option>
                @foreach($bookings as $booking)
                    <option value="{{ $booking->id }}">{{ $booking->booking_code }} - {{ $booking->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label" for="invoice_category">Kategori Invoice</label>
            <select name="invoice_category" id="invoice_category" class="form-control" required>
                <option value="" disabled selected>-- Pilih Kategori --</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="admin-btn admin-btn--primary">Buat Invoice</button>
        <a href="{{ route('admin.bookings.manualInvoiceList') }}" class="admin-btn admin-btn--secondary" style="margin-left:8px;">Batal</a>
    </form>
</div>
@endsection
