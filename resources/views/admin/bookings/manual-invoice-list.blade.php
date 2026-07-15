@extends('admin.layout')

@section('title', 'Invoice Manual')

@section('content')
<div class="admin-topbar">
    <h1>Invoice Manual - Daftar Booking tanpa Invoice</h1>
    <a href="{{ route('admin.dashboard') }}" class="admin-btn admin-btn--secondary">← Kembali ke Dashboard</a>
</div>

<div class="admin-card">
    @if($bookings->isEmpty())
        <p>Semua booking sudah memiliki invoice.</p>
    @else
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Paket</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                    <tr>
                        <td>{{ $booking->booking_code }}</td>
                        <td>{{ $booking->name }}</td>
                        <td>{{ $booking->email }}</td>
                        <td>{{ $booking->tour?->title ?? '—' }}</td>
                        <td>{{ $booking->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.bookings.manualInvoiceForm', $booking) }}" class="admin-btn admin-btn--primary">Buat Invoice</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $bookings->links() }}
    @endif
</div>
@endsection
