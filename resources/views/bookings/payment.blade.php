@extends('layouts.app')

@section('content')
@php
    $whatsAppNumber = \App\Models\Setting::get('phone_whatsapp', '6282137925585');
    $isTransport = $tour->category?->slug === 'transportasi';
@endphp

<style>
    .pay-page { padding: 110px 0 80px; background: var(--bg-main); color: var(--text-primary); min-height: 100vh; }
    .pay-grid { display: grid; grid-template-columns: 1fr 420px; gap: 30px; margin-top: 30px; }
    @media(max-width:991px){ .pay-grid { grid-template-columns: 1fr; } }

    /* Cards */
    .pay-card { background: var(--card-bg,#fff); border-radius: 16px; padding: 28px 30px; box-shadow: 0 8px 30px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.04); margin-bottom: 24px; }
    .pay-card h2 { font-size:1.2rem; font-weight:750; border-bottom:2px solid rgba(0,0,0,0.05); padding-bottom:12px; margin:0 0 20px; display:flex; align-items:center; gap:10px; }
    .pay-card h2 span.num { background:var(--teal,#14B8A6); color:#fff; width:26px; height:26px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:0.85rem; font-weight:800; flex-shrink:0; }

    /* Payment type selector */
    .pay-type-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 24px; }
    .pay-type-option { display: none; }
    .pay-type-label {
        border: 2px solid rgba(0,0,0,0.08);
        border-radius: 12px;
        padding: 18px 16px;
        cursor: pointer;
        transition: all 0.3s;
        text-align: center;
        position: relative;
        background: var(--bg-offset,#f8fafc);
    }
    .pay-type-label:hover { border-color: var(--teal,#14B8A6); transform: translateY(-2px); }
    .pay-type-option:checked + .pay-type-label {
        border-color: var(--teal,#14B8A6);
        background: rgba(20,184,166,0.06);
        box-shadow: 0 0 0 3px rgba(20,184,166,0.12);
    }
    .pay-type-label .check-icon { position:absolute; top:10px; right:10px; width:20px; height:20px; border-radius:50%; border:2px solid rgba(0,0,0,0.15); display:flex; align-items:center; justify-content:center; }
    .pay-type-option:checked + .pay-type-label .check-icon { background:var(--teal,#14B8A6); border-color:var(--teal,#14B8A6); }
    .pay-type-option:checked + .pay-type-label .check-icon::after { content:'✓'; color:#fff; font-size:11px; font-weight:900; }
    .pay-type-label .pay-icon { font-size:2rem; margin-bottom:8px; }
    .pay-type-label .pay-name { font-weight:700; font-size:1rem; margin-bottom:4px; }
    .pay-type-label .pay-amount { font-size:1.15rem; font-weight:800; color:var(--orange,#F97316); }
    .pay-type-label .pay-note { font-size:0.75rem; color:var(--text-muted); margin-top:4px; }

    /* Bank box */
    .bank-box { background:rgba(15,42,82,0.03); border:1px solid rgba(0,0,0,0.06); border-radius:10px; padding:18px; }
    .bank-box .bank-name { font-size:0.78rem; font-weight:800; text-transform:uppercase; letter-spacing:.8px; color:var(--text-muted); margin-bottom:4px; }
    .bank-box .bank-num { font-size:1.5rem; font-weight:800; letter-spacing:2px; color:var(--text-primary); margin-bottom:6px; }
    .bank-box .bank-holder { font-size:0.9rem; color:var(--text-muted); }

    /* Amount banner */
    .amount-banner { border-radius:10px; padding:16px 18px; margin:16px 0; text-align:center; }
    .amount-banner.dp   { background:#FFF7ED; border:1.5px solid #F97316; }
    .amount-banner.lunas { background:#F0FDF4; border:1.5px solid #10B981; }
    .amount-banner .ab-label { font-size:0.85rem; color:var(--text-muted); margin-bottom:4px; }
    .amount-banner .ab-amount { font-size:1.8rem; font-weight:900; }
    .amount-banner.dp .ab-amount   { color: #F97316; }
    .amount-banner.lunas .ab-amount { color: #059669; }

    /* Payment Methods - Vertical Stack */
    .payment-methods-stack {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* Bank Transfer Card */
    .bank-transfer-card {
        background: var(--bg-offset, #f8fafc);
        border: 1.5px solid rgba(0,0,0,0.07);
        border-radius: 14px;
        padding: 18px 20px;
        transition: all 0.25s ease;
    }
    .bank-transfer-card:hover {
        border-color: rgba(0,0,0,0.12);
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    }
    .bank-logo-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    .bank-logo-row img {
        height: 26px;
        width: auto;
    }
    .bank-logo-row .bank-label {
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.5px;
    }
    .bank-account-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin: 6px 0 8px;
    }
    .bank-account-row .account-number {
        font-size: 1.3rem;
        font-weight: 800;
        font-family: 'JetBrains Mono', 'Fira Code', 'Courier New', monospace;
        color: var(--text-primary);
        letter-spacing: 2px;
    }
    .copy-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(20, 184, 166, 0.1);
        border: 1px solid rgba(20, 184, 166, 0.25);
        color: var(--teal, #14B8A6);
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 0.73rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }
    .copy-btn:hover {
        background: var(--teal, #14B8A6);
        color: #fff;
    }
    .copy-btn.copied {
        background: #10B981;
        color: #fff;
        border-color: #10B981;
    }
    .bank-holder-text {
        font-size: 0.85rem;
        color: var(--text-muted);
    }
    .bank-holder-text strong {
        color: var(--text-primary);
    }

    /* QRIS Card */
    .qris-card {
        text-align: center;
        background: var(--bg-offset, #f8fafc);
        border: 1.5px solid rgba(0,0,0,0.07);
        border-radius: 14px;
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: all 0.25s ease;
    }
    .qris-card:hover {
        border-color: rgba(20, 184, 166, 0.4);
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    }
    .qris-img-container {
        position: relative;
        cursor: pointer;
        display: inline-block;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid rgba(20, 184, 166, 0.25);
        background: #fff;
        padding: 6px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.06);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .qris-img-container:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 28px rgba(20, 184, 166, 0.2);
    }
    .qris-img-container img {
        max-width: 180px;
        width: 100%;
        height: auto;
        border-radius: 8px;
        display: block;
    }
    .qris-zoom-badge {
        position: absolute;
        bottom: 12px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(4px);
        color: #ffffff;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
        pointer-events: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        transition: all 0.2s ease;
    }
    .qris-img-container:hover .qris-zoom-badge {
        background: var(--teal, #14B8A6);
    }
    .qris-merchant-info {
        margin-top: 10px;
        text-align: center;
    }
    .qris-merchant-info strong {
        font-size: 0.85rem;
        color: var(--text-primary);
        display: block;
    }
    .qris-merchant-info small {
        font-size: 0.72rem;
        color: var(--text-muted);
        display: block;
        margin-top: 2px;
    }
    .qris-action-buttons {
        display: flex;
        gap: 8px;
        margin-top: 12px;
        width: 100%;
        max-width: 240px;
    }
    .btn-qris-action {
        flex: 1;
        padding: 7px 10px;
        border-radius: 8px;
        font-size: 0.76rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        box-sizing: border-box;
    }
    .btn-qris-zoom {
        background: rgba(20, 184, 166, 0.12);
        color: var(--teal, #14B8A6);
        border: 1px solid rgba(20, 184, 166, 0.25);
    }
    .btn-qris-zoom:hover {
        background: var(--teal, #14B8A6);
        color: #fff;
    }
    .btn-qris-download {
        background: var(--teal, #14B8A6);
        color: #fff;
        border: 1px solid var(--teal, #14B8A6);
    }
    .btn-qris-download:hover {
        background: #0d9488;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
    }
    .qris-hint {
        font-size: 0.73rem;
        color: var(--text-muted);
        margin-top: 10px;
        line-height: 1.4;
        text-align: center;
    }

    /* QRIS Modal Lightbox */
    .qris-modal {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.88);
        backdrop-filter: blur(8px);
        z-index: 999999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .qris-modal.active {
        display: flex;
        animation: qrisFadeIn 0.22s ease-out forwards;
    }
    @keyframes qrisFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .qris-modal-content {
        background: #ffffff;
        border-radius: 18px;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 25px 60px rgba(0,0,0,0.45);
        overflow: hidden;
        animation: qrisScaleUp 0.28s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        position: relative;
    }
    @keyframes qrisScaleUp {
        from { transform: scale(0.92) translateY(16px); opacity: 0; }
        to { transform: scale(1) translateY(0); opacity: 1; }
    }
    .qris-modal-close {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #f1f5f9;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        font-size: 1.4rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #475569;
        transition: all 0.2s;
        line-height: 1;
        z-index: 2;
    }
    .qris-modal-close:hover {
        background: #ef4444;
        color: #fff;
    }
    .qris-modal-header {
        padding: 20px 20px 12px;
        text-align: center;
    }
    .qris-modal-header h3 {
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px;
    }
    .qris-modal-header p {
        font-size: 0.76rem;
        color: #64748b;
        margin: 0;
    }
    .qris-modal-body {
        padding: 0 24px 16px;
        text-align: center;
    }
    .qris-modal-body img {
        width: 100%;
        max-width: 320px;
        height: auto;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        display: block;
        margin: 0 auto;
    }
    .qris-modal-footer {
        padding: 14px 20px 18px;
        display: flex;
        gap: 10px;
        justify-content: center;
        flex-wrap: wrap;
    }

    /* Upload area */
    .upload-area { border:2px dashed rgba(0,0,0,0.1); border-radius:12px; padding:24px; text-align:center; cursor:pointer; transition:all .3s; background:var(--bg-offset,#f8fafc); position:relative; }
    .upload-area:hover { border-color:var(--teal,#14B8A6); background:rgba(20,184,166,0.03); }
    .upload-area input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; }
    .upload-area svg { margin-bottom:10px; color:var(--text-muted); }
    .upload-area p { margin:0; font-size:0.9rem; color:var(--text-muted); }
    .upload-area strong { color:var(--text-primary); }
    #proof-preview { display:none; margin-top:16px; border-radius:10px; overflow:hidden; text-align:center; }
    #proof-preview img { max-width:100%; max-height:280px; object-fit:contain; border-radius:8px; border:1px solid rgba(0,0,0,0.07); }
    #proof-preview .preview-clear { font-size:0.8rem; color:#ef4444; cursor:pointer; margin-top:8px; display:inline-block; font-weight:600; }

    /* Terms */
    .terms-box { background:rgba(15,42,82,0.025); border-left:3px solid var(--teal,#14B8A6); border-radius:0 8px 8px 0; padding:16px 18px; font-size:0.87rem; line-height:1.75; color:var(--text-muted); max-height:260px; overflow-y:auto; }
    .terms-box h4 { color:var(--text-primary); font-size:0.9rem; margin:14px 0 6px; }
    .terms-box ul { margin:0 0 10px; padding-left:18px; }
    .terms-box ol { margin:0 0 10px; padding-left:18px; }

    /* agree */
    .agree-row { display:flex; align-items:flex-start; gap:12px; padding:14px; background:rgba(20,184,166,0.05); border-radius:10px; border:1px solid rgba(20,184,166,0.15); margin-top:16px; }
    .agree-row input[type=checkbox] { width:18px; height:18px; accent-color:var(--teal,#14B8A6); flex-shrink:0; margin-top:2px; cursor:pointer; }
    .agree-row label { font-size:0.88rem; line-height:1.6; color:var(--text-primary); cursor:pointer; }

    /* Summary sidebar */
    .summary-sticky { position:sticky; top:100px; }
    .summary-table { width:100%; border-collapse:collapse; font-size:0.92rem; }
    .summary-table td { padding:10px 0; border-bottom:1px solid rgba(0,0,0,0.05); }
    .summary-table td:first-child { color:var(--text-muted); font-weight:600; }
    .summary-table tr:last-child td { border:none; padding-top:14px; font-size:1.1rem; font-weight:800; }

    /* Submit btn */
    .submit-booking { width:100%; border:none; padding:16px; border-radius:10px; font-size:1rem; font-weight:800; cursor:pointer; background:linear-gradient(135deg,#14B8A6,#0d9488); color:#fff; transition:all .3s; display:flex; align-items:center; justify-content:center; gap:10px; box-shadow:0 6px 20px rgba(20,184,166,0.3); margin-top:8px; }
    .submit-booking:hover { transform:translateY(-2px); box-shadow:0 10px 25px rgba(20,184,166,0.4); }
    .submit-booking:disabled { opacity:.6; cursor:not-allowed; transform:none; }

    /* Steps indicator */
    .steps { display:flex; align-items:center; gap:0; margin-bottom:32px; }
    .step { display:flex; align-items:center; gap:8px; }
    .step-dot { width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:0.85rem; flex-shrink:0; }
    .step-dot.done { background:var(--teal,#14B8A6); color:#fff; }
    .step-dot.current { background:var(--navy,#0F2A52); color:#fff; }
    .step-dot.next { background:rgba(0,0,0,0.08); color:var(--text-muted); }
    .step-label { font-size:0.8rem; font-weight:600; }
    .step-label.done { color:var(--teal,#14B8A6); }
    .step-label.current { color:var(--text-primary); }
    .step-label.next { color:var(--text-muted); }
    .step-line { flex:1; height:2px; background:rgba(0,0,0,0.08); margin:0 8px; min-width:30px; }
    .step-line.done { background:var(--teal,#14B8A6); }

    /* Participant Form Styling */
    .participant-card {
        background: var(--bg-offset, #f8fafc);
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 14px;
        padding: 22px;
        margin-bottom: 20px;
        transition: all 0.25s ease;
    }
    .participant-card:hover {
        border-color: rgba(20, 184, 166, 0.4);
        box-shadow: 0 6px 20px rgba(0,0,0,0.03);
    }
    .participant-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px dashed rgba(0,0,0,0.1);
    }
    .participant-title {
        font-size: 1.02rem;
        font-weight: 750;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .participant-badge {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 3px 10px;
        border-radius: 999px;
        background: rgba(20, 184, 166, 0.12);
        color: var(--teal, #14B8A6);
    }
    .p-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .p-form-full {
        grid-column: 1 / -1;
    }
    @media(max-width: 640px) {
        .p-form-grid {
            grid-template-columns: 1fr;
        }
    }
    .p-form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .p-form-group label {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--text-secondary, #475569);
    }
    .p-form-group label span.req {
        color: #ef4444;
        font-weight: 700;
    }
    .p-form-group input, .p-form-group textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid rgba(0,0,0,0.12);
        border-radius: 9px;
        font-size: 0.9rem;
        background: #fff;
        color: var(--text-primary);
        transition: all 0.2s;
        box-sizing: border-box;
    }
    .p-form-group input:focus, .p-form-group textarea:focus {
        outline: none;
        border-color: var(--teal, #14B8A6);
        box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.15);
    }
    .insurance-notice {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px 18px;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(20, 184, 166, 0.09), rgba(15, 42, 82, 0.05));
        border-left: 4px solid var(--teal, #14B8A6);
        margin-bottom: 24px;
        font-size: 0.86rem;
        line-height: 1.6;
    }
    .insurance-notice-icon {
        font-size: 1.6rem;
        line-height: 1;
        flex-shrink: 0;
    }
</style>

<div class="pay-page">
    <div class="container">

        {{-- Breadcrumb --}}
        <a href="{{ route('tours.show', $tour->slug) }}" style="color:var(--teal,#14B8A6); font-weight:600; text-decoration:none; font-size:0.9rem; display:inline-flex; align-items:center; gap:6px;">
            ← {{ __('Kembali ke Detail Paket') }}
        </a>

        {{-- Step Indicator --}}
        <div class="steps" style="margin-top:20px;">
            <div class="step">
                <div class="step-dot done">✓</div>
                <div class="step-label done">{{ __('Isi Data') }}</div>
            </div>
            <div class="step-line done"></div>
            <div class="step">
                <div class="step-dot current">2</div>
                <div class="step-label current">{{ __('Pembayaran') }}</div>
            </div>
            <div class="step-line"></div>
            <div class="step">
                <div class="step-dot next">3</div>
                <div class="step-label next">{{ __('Konfirmasi') }}</div>
            </div>
        </div>

        @if ($errors->any())
            <div style="background:#FEF2F2; border:1px solid #FCA5A5; border-left:4px solid #EF4444; border-radius:10px; padding:16px 20px; margin-bottom:24px; color:#991B1B;">
                <div style="font-weight:750; font-size:0.95rem; margin-bottom:6px; display:flex; align-items:center; gap:8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ __('Mohon periksa kembali data yang belum lengkap:') }}
                </div>
                <ul style="margin:0; padding-left:24px; font-size:0.86rem; line-height:1.6;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('bookings.store') }}" enctype="multipart/form-data" id="payment-form">
        @csrf

        <div class="pay-grid">
            {{-- ===== LEFT COLUMN ===== --}}
            <div>

                {{-- 1. Pilih Tipe Pembayaran --}}
                <div class="pay-card">
                    <h2><span class="num">1</span> {{ __('Pilih Metode Pembayaran') }}</h2>

                    <div class="pay-type-grid">
                        {{-- DP --}}
                        <div>
                            <input type="radio" name="payment_type" id="type-dp" value="dp" class="pay-type-option" checked
                                onchange="updatePayment()">
                            <label for="type-dp" class="pay-type-label">
                                <div class="check-icon"></div>
                                <div class="pay-icon">💳</div>
                                <div class="pay-name">Down Payment</div>
                                <div class="pay-amount" id="dp-amount-label">Rp {{ number_format(100000 * $draft['participants'], 0, ',', '.') }}</div>
                                <div class="pay-note">Rp 100.000 × {{ $draft['participants'] }} pax</div>
                            </label>
                        </div>
                        {{-- Lunas --}}
                        <div>
                            <input type="radio" name="payment_type" id="type-lunas" value="lunas" class="pay-type-option"
                                onchange="updatePayment()">
                            <label for="type-lunas" class="pay-type-label">
                                <div class="check-icon"></div>
                                <div class="pay-icon">✅</div>
                                <div class="pay-name">{{ __('Bayar Lunas') }}</div>
                                <div class="pay-amount">Rp {{ number_format($fullAmount, 0, ',', '.') }}</div>
                                <div class="pay-note">{{ __('Harga penuh') }}</div>
                            </label>
                        </div>
                    </div>

                    {{-- Dynamic amount banner --}}
                    <div class="amount-banner dp" id="amount-banner-dp">
                        <div class="ab-label">{{ __('Nominal yang Harus Dibayarkan Sekarang:') }}</div>
                        <div class="ab-amount">Rp {{ number_format(100000 * $draft['participants'], 0, ',', '.') }}</div>
                    </div>
                    <div class="amount-banner lunas" id="amount-banner-lunas" style="display:none;">
                        <div class="ab-label">{{ __('Nominal yang Harus Dibayarkan Sekarang:') }}</div>
                        <div class="ab-amount">Rp {{ number_format($fullAmount, 0, ',', '.') }}</div>
                    </div>

                    @if($draft['payment_type'] ?? null === 'dp')
                    <p style="font-size:0.85rem; color:var(--text-muted); line-height:1.6; background:rgba(249,115,22,0.06); padding:12px 14px; border-radius:8px; margin-top:12px; border-left:3px solid #F97316;">
                        ⚠️ {{ __('Dengan memilih DP, sisa pelunasan') }} <strong>Rp {{ number_format($fullAmount - (100000 * $draft['participants']), 0, ',', '.') }}</strong> {{ __('wajib dibayar paling lambat') }} <strong>{{ __('H-1 sebelum keberangkatan') }}</strong>.
                    </p>
                    @endif
                    <p id="dp-warning" style="font-size:0.85rem; color:var(--text-muted); line-height:1.6; background:rgba(249,115,22,0.06); padding:12px 14px; border-radius:8px; margin-top:12px; border-left:3px solid #F97316;">
                        ⚠️ {{ __('Dengan memilih DP, sisa pelunasan') }} <strong>Rp {{ number_format($fullAmount - (100000 * $draft['participants']), 0, ',', '.') }}</strong> {{ __('wajib dibayar paling lambat') }} <strong>{{ __('H-1 sebelum keberangkatan') }}</strong>.
                    </p>
                    <p id="lunas-note" style="font-size:0.85rem; color:#059669; line-height:1.6; background:rgba(16,185,129,0.06); padding:12px 14px; border-radius:8px; margin-top:12px; border-left:3px solid #10B981; display:none;">
                        ✅ {{ __('Dengan bayar lunas, slot Anda langsung diamankan penuh dan tidak ada sisa pembayaran.') }}
                    </p>
                </div>

                {{-- 2. Info Pembayaran --}}
                <div class="pay-card">
                    <h2><span class="num">2</span> {{ __('Informasi Pembayaran') }}</h2>

                    <div class="payment-methods-stack">
                        {{-- Bank Transfer --}}
                        <div class="bank-transfer-card">
                            <div class="bank-logo-row">
                                <img src="{{ asset('images/cimb_niaga.svg') }}" alt="CIMB Niaga">
                                <span class="bank-label">{{ __('Transfer Bank') }}</span>
                            </div>
                            <div class="bank-account-row">
                                <span class="account-number" id="bank-account-num">{{ $bankAccount }}</span>
                                <button type="button" class="copy-btn" id="copy-rekening-btn" onclick="copyRekening()">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    <span id="copy-text">{{ __('Salin') }}</span>
                                </button>
                            </div>
                            <div class="bank-holder-text">a.n. <strong>{{ $bankHolder }}</strong></div>
                            <small style="color:var(--text-muted); font-size:0.73rem; display:block; margin-top:8px; line-height:1.5;">
                                {{ __('Menerima transfer dari seluruh jaringan ATM, Internet Banking, dan Mobile Banking.') }}
                            </small>
                        </div>

                        {{-- QRIS --}}
                        <div class="qris-card">
                            <p style="font-size:0.78rem; font-weight:700; text-transform:uppercase; color:var(--text-muted); letter-spacing:.5px; margin:0 0 10px;">{{ __('Scan / Download QRIS') }}</p>

                            {{-- Total badge above image --}}
                            <div id="qris-amount-badge" style="
                                background: linear-gradient(135deg, #0f766e, #14B8A6);
                                color: #fff;
                                border-radius: 10px;
                                padding: 8px 16px;
                                margin-bottom: 12px;
                                text-align: center;
                                width: 100%;
                                box-sizing: border-box;
                            ">
                                <div style="font-size:0.7rem; font-weight:700; letter-spacing:.5px; text-transform:uppercase; opacity:.85; margin-bottom:2px;" id="qris-amount-label">{{ __('Bayar DP Sekarang') }}</div>
                                <div style="font-size:1.45rem; font-weight:900; letter-spacing:1px; line-height:1.1;" id="qris-amount-value">Rp {{ number_format(100000 * $draft['participants'], 0, ',', '.') }}</div>
                            </div>

                            {{-- Clickable Container to Zoom --}}
                            <div class="qris-img-container" onclick="openQrisModal()" title="{{ __('Klik untuk memperbesar gambar QRIS') }}">
                                <img src="{{ asset('images/qris_travine.png') }}" alt="QRIS TRAVINE.ID MANAGEMENT">
                                <div class="qris-zoom-badge">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                                    <span>{{ __('Klik untuk perbesar') }}</span>
                                </div>
                            </div>

                            <div class="qris-merchant-info">
                                <strong>TRAVINE.ID MANAGEMENT</strong>
                                <small>NMID: ID1026610108908 &bull; Standar QRIS Nasional</small>
                            </div>

                            {{-- Action buttons --}}
                            <div class="qris-action-buttons">
                                <button type="button" class="btn-qris-action btn-qris-zoom" onclick="openQrisModal()">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                                    <span>{{ __('Perbesar') }}</span>
                                </button>
                                <a href="{{ asset('images/qris_travine.png') }}" download="QRIS-TRAVINE-ID-MANAGEMENT.png" class="btn-qris-action btn-qris-download" title="{{ __('Unduh gambar QRIS') }}">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    <span>{{ __('Download QRIS') }}</span>
                                </a>
                            </div>

                            <span class="qris-hint">
                                💡 {{ __('Bisa bayar dari CIMB Niaga, BCA, Mandiri, BRI, BNI, GoPay, OVO, Dana, ShopeePay, dll.') }}
                            </span>
                        </div>
                    </div>

                    <p style="font-size:0.83rem; color:var(--text-muted); margin-top:16px; padding:10px 14px; border-left:3px solid var(--teal,#14B8A6); background:rgba(20,184,166,0.04); border-radius:0 8px 8px 0; line-height:1.6;">
                        {{ __('Harap transfer tepat sesuai nominal di atas. Simpan bukti struk/screenshot transfer dan unggah di bawah ini.') }}
                    </p>
                </div>

                {{-- 3. Data Peserta Asuransi --}}
                <div class="pay-card">
                    <h2><span class="num">3</span> {{ __('Data Peserta Wisata (Asuransi Perjalanan)') }}</h2>

                    <div class="insurance-notice">
                        <div class="insurance-notice-icon">🛡️</div>
                        <div>
                            <strong style="color:var(--text-primary); display:block; margin-bottom:4px;">{{ __('Kebutuhan Registrasi Asuransi Perjalanan') }}</strong>
                            <span style="color:var(--text-secondary,#475569);">
                                {{ __('Demi kenyamanan dan perlindungan keselamatan selama perjalanan, mohon lengkapi identitas seluruh peserta (') }}{{ $draft['participants'] }} {{ __('orang) sesuai KTP / Paspor / KITAS.') }}
                            </span>
                        </div>
                    </div>

                    @for($i = 0; $i < (int)$draft['participants']; $i++)
                    <div class="participant-card">
                        <div class="participant-header">
                            <div class="participant-title">
                                <span>👤</span> {{ __('Peserta') }} {{ $i + 1 }}
                                @if($i === 0)
                                    <span class="participant-badge">{{ __('Pemesan / PIC') }}</span>
                                @endif
                            </div>
                            <span style="font-size:0.75rem; color:var(--text-muted); font-weight:600;">#{{ $i + 1 }} {{ __('dari') }} {{ $draft['participants'] }}</span>
                        </div>

                        <div class="p-form-grid">
                            {{-- Nama Lengkap --}}
                            <div class="p-form-group">
                                <label for="p-name-{{ $i }}">{{ __('Nama Lengkap (Sesuai KTP/Identitas)') }} <span class="req">*</span></label>
                                <input type="text" 
                                       id="p-name-{{ $i }}" 
                                       name="participants[{{ $i }}][name]" 
                                       value="{{ old('participants.'.$i.'.name', $i === 0 ? $draft['name'] : '') }}" 
                                       placeholder="{{ __('Contoh: Budi Santoso') }}" 
                                       required>
                                @error('participants.'.$i.'.name')
                                    <span style="color:#ef4444; font-size:0.75rem;">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- NIK / No. KTP / KITAS / Paspor --}}
                            <div class="p-form-group">
                                <label for="p-nik-{{ $i }}">{{ __('NIK / No. KTP / KITAS') }} <span class="req">*</span></label>
                                <input type="text" 
                                       id="p-nik-{{ $i }}" 
                                       name="participants[{{ $i }}][nik]" 
                                       value="{{ old('participants.'.$i.'.nik') }}" 
                                       placeholder="{{ __('Contoh: 3201xxxxxxxxxxxx') }}" 
                                       required>
                                @error('participants.'.$i.'.nik')
                                    <span style="color:#ef4444; font-size:0.75rem;">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Tanggal Lahir --}}
                            <div class="p-form-group">
                                <label for="p-dob-{{ $i }}">{{ __('Tanggal Lahir') }} <span class="req">*</span></label>
                                <input type="date" 
                                       id="p-dob-{{ $i }}" 
                                       name="participants[{{ $i }}][birth_date]" 
                                       value="{{ old('participants.'.$i.'.birth_date') }}" 
                                       max="{{ date('Y-m-d') }}" 
                                       required>
                                @error('participants.'.$i.'.birth_date')
                                    <span style="color:#ef4444; font-size:0.75rem;">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Nomor Telepon / WA --}}
                            <div class="p-form-group">
                                <label for="p-phone-{{ $i }}">{{ __('Nomor Telepon / WhatsApp') }} <span class="req">*</span></label>
                                <input type="text" 
                                       id="p-phone-{{ $i }}" 
                                       name="participants[{{ $i }}][phone]" 
                                       value="{{ old('participants.'.$i.'.phone', $i === 0 ? $draft['whatsapp'] : '') }}" 
                                       placeholder="{{ __('Contoh: 081234567890') }}" 
                                       required>
                                @error('participants.'.$i.'.phone')
                                    <span style="color:#ef4444; font-size:0.75rem;">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Alamat Lengkap --}}
                            <div class="p-form-group p-form-full">
                                <label for="p-addr-{{ $i }}">{{ __('Alamat Lengkap (Sesuai Domisili / KTP)') }} <span class="req">*</span></label>
                                <textarea id="p-addr-{{ $i }}" 
                                          name="participants[{{ $i }}][address]" 
                                          rows="2" 
                                          placeholder="{{ __('Contoh: Jl. Kebon Sirih No. 10, Jakarta Pusat') }}" 
                                          required>{{ old('participants.'.$i.'.address') }}</textarea>
                                @error('participants.'.$i.'.address')
                                    <span style="color:#ef4444; font-size:0.75rem;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>

                {{-- 4. Upload Bukti --}}
                <div class="pay-card">
                    <h2><span class="num">4</span> {{ __('Upload Bukti Pembayaran') }}</h2>

                    <div class="upload-area" id="upload-area">
                        <input type="file" name="payment_proof" id="payment_proof" accept="image/*" required onchange="previewProof(this)">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <p><strong>{{ __('Klik atau seret file') }}</strong> {{ __('ke sini untuk upload') }}</p>
                        <p style="font-size:0.78rem; margin-top:6px;">{{ __('Format: JPG, PNG, WEBP — Maks 4 MB') }}</p>
                    </div>

                    <div id="proof-preview">
                        <img id="preview-img" src="" alt="{{ __('Preview bukti bayar') }}">
                        <div>
                            <span class="preview-clear" onclick="clearProof()">✕ {{ __('Hapus dan pilih ulang') }}</span>
                        </div>
                    </div>
                </div>

                {{-- 5. Syarat & Ketentuan --}}
                <div class="pay-card">
                    <h2><span class="num">5</span> {{ __('Syarat & Ketentuan Reservasi') }}</h2>
                    <div class="terms-box">
                        <h4>1. {{ __('Reservasi') }}</h4>
                        <ul>
                            <li>{{ __('Reservasi berhasil setelah mengisi formulir pendaftaran dan membayar DP (Down Payment) sebesar') }} <strong>Rp 100.000 {{ __('per peserta') }}</strong>.</li>
                            <li>{{ __('DP sebagai tanda jadi untuk mengamankan slot peserta.') }}</li>
                            <li>{{ __('Sistem') }} <em>first come, first served</em> — {{ __('siapa cepat mendaftar dan membayar DP, diprioritaskan mendapat slot.') }}</li>
                        </ul>
                        <h4>2. {{ __('Pelunasan') }}</h4>
                        <ul>
                            <li>{{ __('Sisa pembayaran wajib dilunasi paling lambat') }} <strong>{{ __('H-1 sebelum keberangkatan') }}</strong>.</li>
                            <li>{{ __('Jika belum dilunasi tanpa konfirmasi, penyelenggara berhak membatalkan reservasi.') }}</li>
                        </ul>
                        <h4>3. {{ __('Kebijakan Refund & Pengalihan Jadwal') }}</h4>
                        <ul>
                            <li>{{ __('Refund 100% jika pembatalan diajukan maksimal') }} <strong>1×24 {{ __('jam setelah pembayaran DP') }}</strong>.</li>
                            <li>{{ __('Setelah batas waktu tersebut: tidak dapat direfund, namun dapat dialihkan jadwal') }} <strong>1 {{ __('kali') }}</strong>.</li>
                        </ul>
                        <h4>4. {{ __('Pembatalan oleh Penyelenggara') }}</h4>
                        <ul>
                            <li>{{ __('Jika trip dibatalkan penyelenggara: refund 100% atau alih jadwal gratis.') }}</li>
                        </ul>
                        <h4>5. Force Majeure</h4>
                        <ul>
                            <li>{{ __('Penyelesaian diutamakan dengan musyawarah / penjadwalan ulang.') }}</li>
                        </ul>
                    </div>

                    <div class="agree-row">
                        <input type="checkbox" name="agree_terms" id="agree_terms" value="1" required>
                        <label for="agree_terms">{{ __('Saya telah membaca dan menyetujui') }} <strong>{{ __('Syarat & Ketentuan Reservasi Open Trip') }}</strong> travine.id {{ __('di atas, termasuk kebijakan DP, pelunasan, dan refund.') }}</label>
                    </div>
                </div>

            </div>

            {{-- ===== RIGHT COLUMN: SUMMARY ===== --}}
            <div class="summary-sticky">
                <div class="pay-card">
                    <h2 style="font-size:1.1rem;"><span class="num" style="background:var(--navy,#0F2A52);">📋</span> {{ __('Ringkasan Pesanan') }}</h2>

                    <div style="display:flex; gap:12px; align-items:center; margin-bottom:20px; padding-bottom:16px; border-bottom:1px solid rgba(0,0,0,0.05);">
                        <div style="flex-shrink:0; width:64px; height:64px; border-radius:10px; overflow:hidden; background:#e2e8f0;">
                            <img src="{{ $tour->thumbnail ? asset('storage/'.$tour->thumbnail) : asset('images/hero_bg.jpg') }}" alt="{{ $tour->title }}" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <div>
                            <div style="font-weight:700; font-size:0.95rem; line-height:1.4;">{{ $tour->title }}</div>
                            <div style="font-size:0.8rem; color:var(--text-muted);">📍 {{ $tour->location }}</div>
                        </div>
                    </div>

                    <table class="summary-table">
                        <tr>
                            <td>{{ __('Pemesan') }}</td>
                            <td style="text-align:right; font-weight:600;">{{ $draft['name'] }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Tanggal') }}</td>
                            <td style="text-align:right; font-weight:600;">{{ \Carbon\Carbon::parse($draft['departure_date'])->translatedFormat('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Peserta') }}</td>
                            <td style="text-align:right; font-weight:600;">{{ $draft['participants'] }} {{ __('orang') }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Harga/pax') }}</td>
                            <td style="text-align:right; font-weight:600;">Rp {{ number_format($tour->price, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Total') }}</td>
                            <td style="text-align:right; color:var(--orange,#F97316);">Rp {{ number_format($fullAmount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Dibayar Sekarang') }}</td>
                            <td style="text-align:right;">
                                <span id="summary-paid" style="font-size:1.25rem; color:var(--teal,#14B8A6); font-weight:900;">
                                    Rp {{ number_format(100000 * $draft['participants'], 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    </table>

                    <div style="margin-top:20px; padding-top:16px; border-top:1px solid rgba(0,0,0,0.05); font-size:0.8rem; color:var(--text-muted); line-height:1.6;" id="sisa-info">
                        {{ __('Sisa pelunasan') }}: <strong style="color:var(--text-primary);">Rp {{ number_format($fullAmount - (100000 * $draft['participants']), 0, ',', '.') }}</strong> ({{ __('wajib H-1 keberangkatan') }})
                    </div>

                    <button type="submit" class="submit-booking" id="submit-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        {{ __('Konfirmasi & Kirim Bukti') }}
                    </button>
                    <p style="font-size:0.78rem; color:var(--text-muted); text-align:center; margin-top:10px; line-height:1.5;">
                        {{ __('Invoice konfirmasi akan dikirim ke') }}<br><strong>{{ $draft['email'] }}</strong>
                    </p>
                </div>

                {{-- WhatsApp Help --}}
                <div class="pay-card" style="text-align:center; padding:20px;">
                    <p style="font-size:0.85rem; color:var(--text-muted); margin:0 0 12px;">{{ __('Ada pertanyaan? Hubungi admin kami:') }}</p>
                    <a href="https://wa.me/{{ $whatsApp }}?text=Halo%20Admin%20travine.id,%20saya%20ingin%20menanyakan%20tentang%20pemesanan%20paket%20{{ urlencode($tour->title) }}" target="_blank"
                       style="display:inline-flex; align-items:center; gap:8px; background:#25D366; color:#fff; padding:10px 20px; border-radius:8px; text-decoration:none; font-weight:700; font-size:0.9rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        {{ __('Chat via WhatsApp') }}
                    </a>
                </div>
            </div>
        </div>

        </form>
    </div>
</div>

{{-- QRIS Zoom Lightbox Modal --}}
<div id="qrisModal" class="qris-modal" onclick="closeQrisModal()">
    <div class="qris-modal-content" onclick="event.stopPropagation()">
        <button type="button" class="qris-modal-close" onclick="closeQrisModal()" aria-label="Tutup">&times;</button>
        <div class="qris-modal-header">
            <h3>QRIS TRAVINE.ID MANAGEMENT</h3>
            <p>NMID: ID1026610108908 &bull; Standar QRIS Nasional</p>
        </div>
        <div class="qris-modal-body">
            <img src="{{ asset('images/qris_travine.png') }}" alt="QRIS TRAVINE.ID MANAGEMENT - Full Preview">
        </div>
        <div class="qris-modal-footer">
            <a href="{{ asset('images/qris_travine.png') }}" download="QRIS-TRAVINE-ID-MANAGEMENT.png" class="btn-qris-action btn-qris-download" style="padding:10px 20px; font-size:0.85rem;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                <span>{{ __('Download QRIS') }}</span>
            </a>
            <button type="button" class="btn-qris-action btn-qris-zoom" onclick="closeQrisModal()" style="padding:10px 20px; font-size:0.85rem;">
                {{ __('Tutup') }}
            </button>
        </div>
        <p style="font-size:0.73rem; color:#64748b; padding:0 20px 16px; text-align:center; margin:0;">
            {{ __('Scan langsung lewat CIMB Niaga, BCA Mobile, Livin, BRImo, GoPay, OVO, Dana, ShopeePay, atau unduh ke galeri untuk scan dari file.') }}
        </p>
    </div>
</div>

<script>
    const dpAmount    = {{ 100000 * $draft['participants'] }};
    const fullAmount  = {{ $fullAmount }};
    const remaining   = fullAmount - dpAmount;

    function copyRekening() {
        const num = document.getElementById('bank-account-num').textContent.trim();
        navigator.clipboard.writeText(num).then(function() {
            const btn = document.getElementById('copy-rekening-btn');
            const txt = document.getElementById('copy-text');
            btn.classList.add('copied');
            txt.textContent = '{{ __('Tersalin!') }}';
            setTimeout(function() {
                btn.classList.remove('copied');
                txt.textContent = '{{ __('Salin') }}';
            }, 2000);
        });
    }

    function openQrisModal() {
        const modal = document.getElementById('qrisModal');
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeQrisModal(e) {
        const modal = document.getElementById('qrisModal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('qrisModal');
            if (modal && modal.classList.contains('active')) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }
    });

    function updatePayment() {
        const isDp = document.getElementById('type-dp').checked;

        // Toggle banners
        document.getElementById('amount-banner-dp').style.display    = isDp ? 'block' : 'none';
        document.getElementById('amount-banner-lunas').style.display  = isDp ? 'none'  : 'block';
        document.getElementById('dp-warning').style.display           = isDp ? 'block' : 'none';
        document.getElementById('lunas-note').style.display           = isDp ? 'none'  : 'block';

        // Update summary
        const paid = isDp ? dpAmount : fullAmount;
        document.getElementById('summary-paid').innerText = 'Rp ' + paid.toLocaleString('id-ID');
        document.getElementById('sisa-info').style.display = isDp ? 'block' : 'none';

        // Update QRIS amount badge
        const qrisLabel = document.getElementById('qris-amount-label');
        const qrisValue = document.getElementById('qris-amount-value');
        if (qrisLabel && qrisValue) {
            qrisLabel.textContent = isDp ? '{{ __('Bayar DP Sekarang') }}' : '{{ __('Bayar Lunas Sekarang') }}';
            qrisValue.textContent = 'Rp ' + paid.toLocaleString('id-ID');
        }
    }

    function previewProof(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('proof-preview').style.display = 'block';
                document.getElementById('upload-area').style.borderColor = '#14B8A6';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearProof() {
        document.getElementById('payment_proof').value = '';
        document.getElementById('proof-preview').style.display = 'none';
        document.getElementById('upload-area').style.borderColor = '';
    }

    // Submit guard: disable button on submit to prevent double-click
    document.getElementById('payment-form').addEventListener('submit', function(e) {
        if (!this.checkValidity()) {
            return;
        }

        const btn = document.getElementById('submit-btn');
        const fileInput = document.getElementById('payment_proof');
        const agreeCheck = document.getElementById('agree_terms');

        if (!fileInput.files.length) {
            e.preventDefault();
            alert('{{ __('Harap upload bukti pembayaran terlebih dahulu.') }}');
            return;
        }
        if (!agreeCheck.checked) {
            e.preventDefault();
            alert('{{ __('Anda harus menyetujui Syarat & Ketentuan untuk melanjutkan.') }}');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.49-4.5"/></svg> {{ __('Memproses...') }}';
    });
</script>

<style>
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
@endsection
