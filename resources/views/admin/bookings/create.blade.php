@extends('admin.layout')

@section('title', 'Buat Booking Manual')

@section('content')
<div class="admin-topbar">
    <h1>Buat Booking Manual</h1>
    <a href="{{ route('admin.bookings.index') }}" class="admin-btn admin-btn--secondary">← Kembali</a>
</div>

<div class="admin-card" style="max-width: 800px;">
    <form method="POST" action="{{ route('admin.bookings.store') }}">
        @csrf

        {{-- Data Pelanggan --}}
        <h3 style="margin-bottom: 16px; color: var(--teal);">📋 Data Pelanggan</h3>
        <div class="admin-grid admin-grid--2" style="gap: 16px; margin-bottom: 24px;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="name">Nama Lengkap <span style="color:#ef4444;">*</span></label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required placeholder="Nama pelanggan">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="email">Email <span style="color:#ef4444;">*</span></label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required placeholder="email@contoh.com">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="whatsapp">No. WhatsApp <span style="color:#ef4444;">*</span></label>
                <input type="text" name="whatsapp" id="whatsapp" class="form-control" value="{{ old('whatsapp') }}" required placeholder="08xxxxxxxxxx">
            </div>
        </div>

        {{-- Detail Wisata --}}
        <h3 style="margin-bottom: 16px; color: var(--teal);">🌍 Detail Wisata / Transportasi</h3>
        <div class="admin-grid admin-grid--2" style="gap: 16px; margin-bottom: 24px;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="tour_id">Paket <span style="color:#ef4444;">*</span></label>
                <select name="tour_id" id="tour_id" class="form-control" required>
                    <option value="" disabled selected>-- Pilih Paket --</option>
                    @foreach($tours as $tour)
                        <option value="{{ $tour->id }}"
                            data-price="{{ $tour->price }}"
                            data-duration="{{ $tour->duration }}"
                            data-category-slug="{{ $tour->category?->slug }}"
                            {{ old('tour_id') == $tour->id ? 'selected' : '' }}>
                            {{ $tour->title }} — Rp {{ number_format($tour->price, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="departure_date">Tanggal Berangkat / Mulai Sewa <span style="color:#ef4444;">*</span></label>
                <input type="date" name="departure_date" id="departure_date" class="form-control" value="{{ old('departure_date') }}" required>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="duration">Keterangan Durasi</label>
                <input type="text" name="duration" id="duration" class="form-control" value="{{ old('duration') }}" placeholder="Contoh: 3 Hari / 3 Hari 2 Malam">
            </div>

            {{-- Untuk transport: Jumlah Hari --}}
            <div class="form-group" id="group-duration-days" style="margin-bottom: 0; display:none;">
                <label class="form-label" for="duration_days">Jumlah Hari Sewa <span style="color:#ef4444;">*</span></label>
                <input type="number" name="duration_days" id="duration_days" class="form-control" value="{{ old('duration_days', 1) }}" min="1" placeholder="Jumlah hari sewa">
            </div>

            {{-- Untuk wisata: Jumlah Peserta --}}
            <div class="form-group" id="group-participants" style="margin-bottom: 0;">
                <label class="form-label" for="participants">Jumlah Peserta <span style="color:#ef4444;">*</span></label>
                <input type="number" name="participants" id="participants" class="form-control" value="{{ old('participants', 1) }}" min="1">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="discount">Diskon (Rp)</label>
                <input type="number" name="discount" id="discount" class="form-control" value="{{ old('discount', 0) }}" min="0" placeholder="0">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="total_price">Total Harga Akhir (Rp)</label>
                <input type="number" name="total_price" id="total_price" class="form-control" style="font-weight: 700; color: var(--teal);" value="{{ old('total_price', 0) }}" min="0">
            </div>
        </div>

        {{-- Catatan --}}
        <div class="form-group">
            <label class="form-label" for="notes">Catatan (opsional)</label>
            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Permintaan khusus, info tambahan...">{{ old('notes') }}</textarea>
        </div>

        {{-- Status --}}
        <h3 style="margin-bottom: 16px; color: var(--teal);">⚙️ Status</h3>
        <div class="admin-grid admin-grid--2" style="gap: 16px; margin-bottom: 24px;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="booking_status">Status Booking <span style="color:#ef4444;">*</span></label>
                <select name="booking_status" id="booking_status" class="form-control" required>
                    <option value="waiting_payment" {{ old('booking_status', 'waiting_payment') == 'waiting_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="waiting_verification" {{ old('booking_status') == 'waiting_verification' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="processing" {{ old('booking_status') == 'processing' ? 'selected' : '' }}>Diproses</option>
                    <option value="confirmed" {{ old('booking_status') == 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                    <option value="departed" {{ old('booking_status') == 'departed' ? 'selected' : '' }}>Berangkat</option>
                    <option value="completed" {{ old('booking_status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ old('booking_status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="payment_status">Status Pembayaran <span style="color:#ef4444;">*</span></label>
                <select name="payment_status" id="payment_status" class="form-control" required>
                    <option value="unpaid" {{ old('payment_status', 'unpaid') == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                    <option value="dp_paid" {{ old('payment_status') == 'dp_paid' ? 'selected' : '' }}>DP Dibayar</option>
                    <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                    <option value="refund" {{ old('payment_status') == 'refund' ? 'selected' : '' }}>Refund</option>
                    <option value="failed" {{ old('payment_status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                </select>
            </div>
        </div>

        <div style="display: flex; gap: 12px; padding-top: 8px; border-top: 1px solid var(--glass-border);">
            <button type="submit" class="admin-btn admin-btn--primary">💾 Simpan Booking</button>
            <a href="{{ route('admin.bookings.index') }}" class="admin-btn admin-btn--secondary">Batal</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const tourSelect       = document.getElementById('tour_id');
    const participantsInput = document.getElementById('participants');
    const durationDaysInput = document.getElementById('duration_days');
    const discountInput    = document.getElementById('discount');
    const durationInput    = document.getElementById('duration');
    const totalPriceInput  = document.getElementById('total_price');
    const groupParticipants = document.getElementById('group-participants');
    const groupDurationDays = document.getElementById('group-duration-days');

    function isTransport() {
        const selected = tourSelect.options[tourSelect.selectedIndex];
        return selected && selected.dataset.categorySlug === 'transportasi';
    }

    function hitungTotal() {
        const selected = tourSelect.options[tourSelect.selectedIndex];
        const price    = selected ? parseInt(selected.dataset.price || 0) : 0;
        const discount = parseInt(discountInput.value) || 0;
        let subtotal;

        if (isTransport()) {
            const days = parseInt(durationDaysInput.value) || 0;
            subtotal = price * days;
        } else {
            const participants = parseInt(participantsInput.value) || 1;
            subtotal = price * participants;
        }

        totalPriceInput.value = Math.max(0, subtotal - discount);
    }

    function onTourChange() {
        const selected = tourSelect.options[tourSelect.selectedIndex];
        if (!selected) return;

        // Auto-fill keterangan durasi
        durationInput.value = selected.dataset.duration || '';

        // Tampilkan field yang sesuai
        if (isTransport()) {
            groupParticipants.style.display = 'none';
            participantsInput.removeAttribute('required');
            groupDurationDays.style.display = '';
            durationDaysInput.setAttribute('required', 'required');
        } else {
            groupDurationDays.style.display = 'none';
            durationDaysInput.removeAttribute('required');
            groupParticipants.style.display = '';
            participantsInput.setAttribute('required', 'required');
        }

        hitungTotal();
    }

    tourSelect.addEventListener('change', onTourChange);
    participantsInput.addEventListener('input', hitungTotal);
    durationDaysInput.addEventListener('input', hitungTotal);
    discountInput.addEventListener('input', hitungTotal);

    // Inisialisasi
    onTourChange();
    hitungTotal();
</script>
@endpush
@endsection
