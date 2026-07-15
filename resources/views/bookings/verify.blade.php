@extends('layouts.app')

@section('content')
<style>
    .verify-page {
        padding: 120px 0 80px;
        background: var(--bg-offset, #f8fafc);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .verify-card {
        background: var(--card-bg, #fff);
        border-radius: 24px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.06);
        max-width: 500px;
        width: 100%;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.04);
    }
    .verify-header {
        padding: 40px 30px 30px;
        text-align: center;
    }
    .verify-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin-bottom: 20px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }
    .icon--success {
        background: #D1FAE5;
        color: #10B981;
    }
    .icon--warning {
        background: #FEF3C7;
        color: #F59E0B;
    }
    .icon--danger {
        background: #FEE2E2;
        color: #EF4444;
    }

    .ticket-stub {
        border-top: 2px dashed rgba(0,0,0,0.08);
        position: relative;
        padding: 30px;
        background: rgba(15,42,82,0.01);
    }
    .ticket-stub::before, .ticket-stub::after {
        content: '';
        position: absolute;
        top: -10px;
        width: 20px;
        height: 20px;
        background: var(--bg-offset, #f8fafc);
        border-radius: 50%;
    }
    .ticket-stub::before {
        left: -10px;
    }
    .ticket-stub::after {
        right: -10px;
    }

    .verify-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    .verify-table td {
        padding: 10px 0;
        font-size: 0.95rem;
    }
    .verify-table td:first-child {
        color: var(--text-muted);
        font-weight: 500;
        width: 40%;
    }
    .verify-table td:last-child {
        color: var(--text-primary);
        font-weight: 600;
        text-align: right;
    }
</style>

<div class="verify-page">
    <div class="verify-card">
        @php
            $isVerified = in_array($booking->booking_status, ['confirmed', 'completed', 'departed']);
            $isCancelled = $booking->booking_status == 'cancelled';
        @endphp

        <div class="verify-header">
            @if($isVerified)
                <div class="verify-icon icon--success">✓</div>
                <h1 style="font-size: 1.6rem; font-weight: 850; color:#10B981; margin:0 0 8px;">E-Ticket Valid & Sah</h1>
                <span style="background: rgba(16,185,129,0.1); color: #10B981; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">
                    Verified
                </span>
            @elseif($isCancelled)
                <div class="verify-icon icon--danger">✗</div>
                <h1 style="font-size: 1.6rem; font-weight: 850; color:#EF4444; margin:0 0 8px;">Pemesanan Dibatalkan</h1>
                <span style="background: rgba(239,68,68,0.1); color: #EF4444; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">
                    Cancelled
                </span>
            @else
                <div class="verify-icon icon--warning">⏳</div>
                <h1 style="font-size: 1.6rem; font-weight: 850; color:#F59E0B; margin:0 0 8px;">Menunggu Verifikasi</h1>
                <span style="background: rgba(245,158,11,0.1); color: #F59E0B; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">
                    Pending
                </span>
            @endif
        </div>

        <div class="ticket-stub">
            <h2 style="font-size: 1.1rem; font-weight: 750; margin: 0 0 15px; color: var(--text-primary);">Rincian Perjalanan</h2>
            
            <table class="verify-table">
                <tr>
                    <td>Kode Booking</td>
                    <td>{{ $booking->booking_code }}</td>
                </tr>
                @if($booking->invoice_number)
                <tr>
                    <td>No. Invoice</td>
                    <td>{{ $booking->invoice_number }}</td>
                </tr>
                @endif
                <tr>
                    <td>Paket Wisata</td>
                    <td style="color:var(--teal, #14B8A6) !important;">{{ $booking->tour->title }}</td>
                </tr>
                <tr>
                    <td>Nama Peserta</td>
                    <td>{{ $booking->name }}</td>
                </tr>
                <tr>
                    <td>Jumlah Peserta</td>
                    <td>{{ $booking->participants }} Orang</td>
                </tr>
                <tr>
                    <td>Tgl Keberangkatan</td>
                    <td>{{ $booking->departure_date ? $booking->departure_date->translatedFormat('d F Y') : '-' }}</td>
                </tr>
                <tr>
                    <td>Status Bayar</td>
                    <td style="color: {{ $booking->payment_status == 'paid' ? '#10B981' : '#F59E0B' }};">
                        {{ $booking->payment_status_label }}
                    </td>
                </tr>
            </table>

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('home') }}" class="btn btn--secondary" style="text-decoration: none; font-size:0.9rem; padding:8px 16px;">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
