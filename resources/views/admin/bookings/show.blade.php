@extends('admin.layout')
@section('title', 'Detail Pemesanan — ' . $booking->booking_code)
@section('content')
<div class="admin-topbar">
    <div>
        <h1>{{ $booking->booking_code }}</h1>
        <p style="color:var(--text-muted); font-size:0.85rem; margin-top:4px;">Dipesan {{ $booking->created_at->format('d M Y, H:i') }}</p>
    </div>
    <div style="display:flex; gap:8px;">
        @if(!$booking->invoice_number)
                <a href="{{ route('admin.bookings.manualInvoiceForm', $booking) }}" class="admin-btn admin-btn--primary">Generate Manual Invoice</a>
            @else
                <a href="{{ route('admin.bookings.invoice', $booking) }}" class="admin-btn admin-btn--secondary">↓ Invoice PDF</a>
            @endif
        <form method="POST" action="{{ route('admin.bookings.resend', $booking) }}" style="display:inline;">
            @csrf
            <button type="submit" class="admin-btn admin-btn--primary">Kirim Ulang Invoice</button>
        </form>
        <a href="{{ route('admin.bookings.index') }}" class="admin-btn admin-btn--secondary">← Kembali</a>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 340px; gap:20px; align-items:start;">
    <div style="display:flex; flex-direction:column; gap:20px;">
        {{-- Customer Info --}}
        @php
            $isTransport = $booking->tour?->category?->slug === 'transportasi';
            $subtotal = $booking->total_price + $booking->discount;
        @endphp
        <div class="admin-card">
            <h3 style="font-weight:700; margin-bottom:16px;">Informasi Pemesan</h3>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div><div style="font-size:0.78rem; color:var(--text-muted); margin-bottom:4px;">Nama Lengkap</div><div style="font-weight:600;">{{ $booking->name }}</div></div>
                <div><div style="font-size:0.78rem; color:var(--text-muted); margin-bottom:4px;">WhatsApp</div><a href="https://wa.me/{{ $booking->whatsapp }}" target="_blank" style="font-weight:600; color:var(--teal);">{{ $booking->whatsapp }}</a></div>
                <div><div style="font-size:0.78rem; color:var(--text-muted); margin-bottom:4px;">Email</div><div style="font-weight:600;">{{ $booking->email }}</div></div>
                @if($isTransport)
                    <div><div style="font-size:0.78rem; color:var(--text-muted); margin-bottom:4px;">Durasi Sewa</div><div style="font-weight:600;">{{ $booking->duration_days }} hari</div></div>
                @else
                    <div><div style="font-size:0.78rem; color:var(--text-muted); margin-bottom:4px;">Jumlah Peserta</div><div style="font-weight:600;">{{ $booking->participants }} orang</div></div>
                @endif
                <div><div style="font-size:0.78rem; color:var(--text-muted); margin-bottom:4px;">Tgl Keberangkatan</div><div style="font-weight:600;">{{ $booking->departure_date->format('d M Y') }}</div></div>
                <div>
                    <div style="font-size:0.78rem; color:var(--text-muted); margin-bottom:4px;">Total Biaya</div>
                    @if($booking->discount > 0)
                        <div style="font-size:0.8rem; text-decoration: line-through; color: var(--text-muted);">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </div>
                        <div style="font-size:0.8rem; color: #ef4444; margin-bottom: 2px;">
                            Diskon: -{{ $booking->discount_formatted }}
                        </div>
                    @endif
                    <div style="font-weight:700; color:var(--teal); font-size:1.1rem;">{{ $booking->total_price_formatted }}</div>
                </div>
            </div>
            @if($booking->notes)
                <div style="margin-top:16px; padding:12px; background:rgba(255,255,255,0.04); border-radius:8px;">
                    <div style="font-size:0.78rem; color:var(--text-muted); margin-bottom:4px;">Catatan</div>
                    <div>{{ $booking->notes }}</div>
                </div>
            @endif
        </div>

        {{-- Data Peserta Asuransi --}}
        @if(!empty($booking->participants_data) && is_array($booking->participants_data))
        <div class="admin-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <h3 style="font-weight:700; margin:0; display:flex; align-items:center; gap:8px;">
                    <span>🛡️</span> Data Peserta Asuransi ({{ count($booking->participants_data) }} Orang)
                </h3>
                <span class="badge badge-info">{{ count($booking->participants_data) }} Peserta</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="admin-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Nama Lengkap</th>
                            <th>NIK / Identitas</th>
                            <th>Tgl Lahir</th>
                            <th>Telepon</th>
                            <th>Alamat</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($booking->participants_data as $idx => $p)
                        <tr>
                            <td style="font-weight:700;">{{ $idx + 1 }}</td>
                            <td>
                                <div style="font-weight:600; color:var(--text-primary);">{{ $p['name'] ?? '-' }}</div>
                                @if($idx === 0)
                                    <span style="font-size:0.7rem; color:var(--teal); font-weight:700;">PIC / Pemesan</span>
                                @endif
                            </td>
                            <td><code style="font-size:0.85rem;">{{ $p['nik'] ?? '-' }}</code></td>
                            <td>{{ !empty($p['birth_date']) ? \Carbon\Carbon::parse($p['birth_date'])->format('d/m/Y') : '-' }}</td>
                            <td>
                                @if(!empty($p['phone']))
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $p['phone']) }}" target="_blank" style="color:var(--teal); text-decoration:none;">{{ $p['phone'] }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td style="font-size:0.82rem; max-width:250px;">{{ $p['address'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Tour Info --}}
        <div class="admin-card">
            <h3 style="font-weight:700; margin-bottom:16px;">Paket Wisata</h3>
            <div style="display:flex; gap:16px; align-items:center;">
                @if($booking->tour?->thumbnail)
                    <img src="{{ Storage::url($booking->tour->thumbnail) }}" style="width:100px; height:70px; object-fit:cover; border-radius:8px; flex-shrink:0;">
                @endif
                <div>
                    <div style="font-weight:700; font-size:1rem;">{{ $booking->tour?->title }}</div>
                    <div style="color:var(--text-muted); font-size:0.85rem; margin-top:4px;">📍 {{ $booking->tour?->location }}</div>
                    <div style="color:var(--text-muted); font-size:0.85rem;">⏱️ {{ $booking->duration ?? $booking->tour?->duration }}</div>
                </div>
            </div>
        </div>

        {{-- Payment Proof --}}
        @if($booking->payment_proof)
        <div class="admin-card">
            <h3 style="font-weight:700; margin-bottom:16px;">Bukti Pembayaran</h3>
            <img src="{{ Storage::url($booking->payment_proof) }}" style="max-width:100%; border-radius:8px; max-height:400px; object-fit:contain;">
        </div>
        @endif

        {{-- Notification Logs --}}
        @if($booking->notificationLogs->count())
        <div class="admin-card">
            <h3 style="font-weight:700; margin-bottom:16px;">Log Notifikasi</h3>
            <table class="admin-table">
                <thead><tr><th>Tipe</th><th>Penerima</th><th>Status</th><th>Waktu</th></tr></thead>
                <tbody>
                @foreach($booking->notificationLogs as $log)
                    <tr>
                        <td><span class="badge badge-info">{{ $log->type }}</span></td>
                        <td>{{ $log->recipient }}</td>
                        <td><span class="badge {{ $log->status === 'success' ? 'badge-success' : 'badge-danger' }}">{{ $log->status }}</span></td>
                        <td style="font-size:0.82rem; color:var(--text-muted);">{{ $log->created_at->format('d M Y H:i') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Right Panel --}}
    <div style="display:flex; flex-direction:column; gap:16px;">
        {{-- Update Status --}}
        <div class="admin-card">
            <h3 style="font-weight:700; margin-bottom:16px;">Update Status Booking</h3>
            <form method="POST" action="{{ route('admin.bookings.status', $booking) }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="booking_status" class="form-control">
                        @foreach(['waiting_payment'=>'Menunggu Pembayaran','waiting_verification'=>'Menunggu Verifikasi','processing'=>'Diproses','confirmed'=>'Dikonfirmasi','departed'=>'Berangkat','completed'=>'Selesai','cancelled'=>'Dibatalkan'] as $v=>$l)
                            <option value="{{ $v }}" {{ $booking->booking_status === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Catatan Admin</label>
                    <textarea name="admin_notes" class="form-control" rows="3" placeholder="Catatan internal...">{{ $booking->admin_notes }}</textarea>
                </div>
                <button type="submit" class="admin-btn admin-btn--primary" style="width:100%; justify-content:center;">Update Status</button>
            </form>
        </div>

        {{-- Update Payment --}}
        <div class="admin-card">
            <h3 style="font-weight:700; margin-bottom:16px;">Status Pembayaran</h3>
            <form method="POST" action="{{ route('admin.bookings.payment', $booking) }}">
                @csrf
                <div class="form-group">
                    <select name="payment_status" class="form-control">
                        @foreach(['unpaid'=>'Belum Bayar','dp_paid'=>'DP Dibayar','paid'=>'Lunas','refund'=>'Refund','failed'=>'Gagal'] as $v=>$l)
                            <option value="{{ $v }}" {{ $booking->payment_status === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="admin-btn admin-btn--warning" style="width:100%; justify-content:center;">Update Pembayaran</button>
            </form>
        </div>

        {{-- Delete --}}
        <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}" onsubmit="return confirm('Yakin hapus pemesanan ini? Data tidak bisa dipulihkan.')">
            @csrf @method('DELETE')
            <button type="submit" class="admin-btn admin-btn--danger" style="width:100%; justify-content:center;">Hapus Pemesanan</button>
        </form>
    </div>
</div>
@endsection
