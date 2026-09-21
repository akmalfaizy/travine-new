@extends('layouts.app')

@section('content')
<style>
    .track-page {
        min-height: 100vh;
        padding: 120px 0 80px;
        background: var(--bg-main);
        color: var(--text-primary);
        position: relative;
        overflow: hidden;
    }

    /* Ambient background blobs */
    .track-page::before {
        content: '';
        position: absolute;
        top: -10%;
        left: -5%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(20,184,166,0.12) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .track-page::after {
        content: '';
        position: absolute;
        bottom: -10%;
        right: -5%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(99,102,241,0.10) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .track-container {
        max-width: 560px;
        margin: 0 auto;
        padding: 0 20px;
        position: relative;
        z-index: 1;
    }

    /* Header */
    .track-header {
        text-align: center;
        margin-bottom: 40px;
    }
    .track-icon-wrap {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 72px;
        height: 72px;
        border-radius: 20px;
        background: linear-gradient(135deg, #14B8A6, #0EA5E9);
        box-shadow: 0 8px 30px rgba(20,184,166,0.35);
        margin-bottom: 20px;
        animation: floatIcon 3s ease-in-out infinite;
    }
    @keyframes floatIcon {
        0%, 100% { transform: translateY(0); }
        50%       { transform: translateY(-6px); }
    }
    .track-header h1 {
        font-size: 2rem;
        font-weight: 800;
        margin: 0 0 10px;
        line-height: 1.2;
    }
    .track-header p {
        color: var(--text-muted);
        font-size: 1rem;
        line-height: 1.6;
        margin: 0;
    }

    /* Card */
    .track-card {
        background: var(--card-bg, #fff);
        border-radius: 20px;
        padding: 36px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.07);
        border: 1px solid rgba(0,0,0,0.05);
        margin-bottom: 24px;
    }

    /* Input group */
    .track-input-group {
        display: flex;
        gap: 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 2px solid rgba(0,0,0,0.06);
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .track-input-group:focus-within {
        border-color: #14B8A6;
        box-shadow: 0 4px 20px rgba(20,184,166,0.15);
    }
    .track-input {
        flex: 1;
        padding: 16px 20px;
        font-size: 1.25rem;
        font-weight: 700;
        letter-spacing: 4px;
        text-transform: uppercase;
        border: none;
        outline: none;
        background: var(--card-bg, #fff);
        color: var(--text-primary);
        font-family: 'Poppins', monospace;
        min-width: 0;
    }
    .track-input::placeholder {
        letter-spacing: 2px;
        font-weight: 500;
        color: var(--text-muted);
        opacity: 0.45;
    }
    .track-btn {
        padding: 16px 24px;
        background: linear-gradient(135deg, #14B8A6 0%, #0EA5E9 100%);
        color: #fff;
        border: none;
        cursor: pointer;
        font-weight: 700;
        font-size: 0.92rem;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: opacity 0.2s, transform 0.1s;
        font-family: 'Poppins', sans-serif;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .track-btn:hover { opacity: 0.88; }
    .track-btn:active { transform: scale(0.97); }
    .track-btn:disabled { opacity: 0.65; cursor: not-allowed; }

    /* Error state */
    .track-error {
        margin-top: 14px;
        padding: 14px 18px;
        background: #FEE2E2;
        color: #991B1B;
        border-radius: 10px;
        border-left: 4px solid #EF4444;
        font-size: 0.88rem;
        font-weight: 500;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        line-height: 1.5;
    }
    .track-error svg { flex-shrink: 0; margin-top: 1px; }

    /* Steps / panduan */
    .track-steps {
        background: var(--card-bg, #fff);
        border-radius: 20px;
        padding: 28px 36px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.04);
        border: 1px solid rgba(0,0,0,0.04);
    }
    .track-steps__title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: var(--text-muted);
        margin-bottom: 18px;
    }
    .track-step {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 12px 0;
        border-bottom: 1px solid rgba(0,0,0,0.04);
    }
    .track-step:last-child { border-bottom: none; padding-bottom: 0; }
    .track-step:first-of-type { padding-top: 0; }
    .track-step__num {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
    .track-step__num--1 { background: rgba(20,184,166,0.12); color: #14B8A6; }
    .track-step__num--2 { background: rgba(99,102,241,0.12); color: #6366F1; }
    .track-step__num--3 { background: rgba(249,115,22,0.12); color: #F97316; }
    .track-step__content strong { display: block; font-weight: 700; font-size: 0.88rem; margin-bottom: 3px; }
    .track-step__content span { font-size: 0.8rem; color: var(--text-muted); line-height: 1.55; }

    /* Spin animation for loading */
    @keyframes trackSpin {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
    }
    .spin-icon { animation: trackSpin 0.9s linear infinite; }
</style>

<div class="track-page">
    <div class="track-container">

        {{-- Header --}}
        <div class="track-header">
            <div class="track-icon-wrap">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="M21 21l-4.35-4.35"/>
                </svg>
            </div>
            <h1>{{ __('Cek Status Pemesanan') }}</h1>
            <p>{{ __('Masukkan kode booking yang Anda terima untuk melihat detail dan status terbaru pesanan Anda.') }}</p>
        </div>

        {{-- Form Card --}}
        <div class="track-card">
            <label for="booking_code_input" style="display:block; font-weight:700; font-size:0.8rem; margin-bottom:8px; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted);">
                {{ __('Kode Booking') }}
            </label>
            <p style="font-size:0.8rem; color:var(--text-muted); margin-bottom:16px; line-height:1.5;">
                {{ __('Kode 6 karakter dikirimkan ke email Anda saat pemesanan berhasil — contoh:') }}
                <strong style="font-family: monospace; letter-spacing:2px; color:var(--text-primary);">AS35FA</strong>
            </p>

            <form method="POST" action="{{ route('bookings.track.search') }}" id="track-form">
                @csrf
                <div class="track-input-group">
                    <input
                        type="text"
                        id="booking_code_input"
                        name="booking_code"
                        class="track-input"
                        placeholder="A B C 1 2 3"
                        value="{{ old('booking_code') }}"
                        maxlength="10"
                        autocomplete="off"
                        autocapitalize="characters"
                        spellcheck="false"
                        autofocus
                    >
                    <button type="submit" class="track-btn" id="track-submit-btn">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" id="track-btn-icon">
                            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                        </svg>
                        <span id="track-btn-text">{{ __('Cari') }}</span>
                    </button>
                </div>

                {{-- Error Message --}}
                @error('booking_code')
                    <div class="track-error">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </form>

            <p style="font-size:0.77rem; color:var(--text-muted); margin-top:18px; text-align:center; line-height:1.6;">
                {{ __('Tidak ingat kode booking? Cek email masuk Anda, atau') }}
                <a href="https://wa.me/{{ \App\Models\Setting::get('phone_whatsapp', '6282137925585') }}" target="_blank" style="color:#14B8A6; font-weight:600; text-decoration:none;">{{ __('hubungi admin via WhatsApp') }}</a>.
            </p>
        </div>

        {{-- Panduan Langkah --}}
        <div class="track-steps">
            <div class="track-steps__title">📋 {{ __('Cara Mendapatkan Kode Booking') }}</div>

            <div class="track-step">
                <div class="track-step__num track-step__num--1">1</div>
                <div class="track-step__content">
                    <strong>{{ __('Lakukan Pemesanan') }}</strong>
                    <span>{{ __('Pilih paket wisata dan isi formulir pemesanan, lalu unggah bukti pembayaran.') }}</span>
                </div>
            </div>

            <div class="track-step">
                <div class="track-step__num track-step__num--2">2</div>
                <div class="track-step__content">
                    <strong>{{ __('Terima Kode Booking') }}</strong>
                    <span>{{ __('Kode booking 6 karakter unik tampil di halaman konfirmasi dan dikirim ke email Anda secara otomatis.') }}</span>
                </div>
            </div>

            <div class="track-step">
                <div class="track-step__num track-step__num--3">3</div>
                <div class="track-step__content">
                    <strong>{{ __('Pantau Status & Unduh Invoice') }}</strong>
                    <span>{{ __('Masukkan kode di atas untuk melihat status terkini. Setelah dikonfirmasi admin, invoice PDF siap diunduh.') }}</span>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // Auto-uppercase & filter non-alphanumeric
    const inp = document.getElementById('booking_code_input');
    if (inp) {
        inp.addEventListener('input', function () {
            const pos = this.selectionStart;
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            this.setSelectionRange(pos, pos);
        });
    }

    // Prevent double submit + show loading state
    const form = document.getElementById('track-form');
    const btn  = document.getElementById('track-submit-btn');
    const icon = document.getElementById('track-btn-icon');
    const text = document.getElementById('track-btn-text');

    if (form && btn) {
        form.addEventListener('submit', function () {
            btn.disabled = true;
            icon.outerHTML = '<svg class="spin-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/><circle cx="12" cy="12" r="10" opacity="0.2"/></svg>';
            if (text) text.textContent = '{{ __("Mencari...") }}';
        });
    }
</script>
@endsection
