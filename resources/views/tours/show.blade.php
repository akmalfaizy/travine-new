@extends('layouts.app')

@section('content')
<style>
    .tour-detail-page {
        padding: 120px 0 80px;
        background: var(--bg-main);
        color: var(--text-primary);
    }
    .tour-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 40px;
        margin-top: 30px;
    }
    @media (max-width: 991px) {
        .tour-container {
            grid-template-columns: 1fr;
        }
    }
    /* Gallery Styles */
    .tour-gallery {
        background: var(--card-bg);
        border: 1px solid var(--glass-border);
        padding: 16px;
        border-radius: 16px;
        box-shadow: var(--shadow);
        margin-bottom: 30px;
    }
    .main-image-wrapper {
        width: 100%;
        height: 450px;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 12px;
    }
    .main-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }
    .gallery-thumbs {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding-bottom: 8px;
    }
    .thumb-item {
        width: 90px;
        height: 60px;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
        flex-shrink: 0;
        transition: all 0.3s;
    }
    .thumb-item.active, .thumb-item:hover {
        border-color: var(--teal, #14B8A6);
        opacity: 0.8;
    }
    .thumb-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Tabs Styles */
    .tour-tabs {
        background: var(--card-bg);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        box-shadow: var(--shadow);
        overflow: hidden;
        margin-bottom: 40px;
    }
    .tabs-header {
        display: flex;
        border-bottom: 1px solid var(--glass-border);
        background: rgba(255,255,255,0.02);
        overflow-x: auto;
    }
    .tab-btn {
        padding: 18px 24px;
        background: none;
        border: none;
        font-family: inherit;
        font-weight: 600;
        font-size: 1rem;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.3s;
        border-bottom: 3px solid transparent;
        white-space: nowrap;
    }
    .tab-btn:hover {
        color: var(--teal, #14B8A6);
    }
    .tab-btn.active {
        color: var(--teal, #14B8A6);
        border-bottom-color: var(--teal, #14B8A6);
        background: var(--card-bg);
    }
    .tab-pane {
        padding: 30px;
        display: none;
        line-height: 1.7;
        color: var(--text-primary);
    }
    .tab-pane.active {
        display: block;
    }
    .tab-pane h3 {
        margin-bottom: 16px;
        font-weight: 700;
        font-size: 1.3rem;
        color: var(--text-primary);
    }

    /* Booking Form Sticky Sidebar */
    .booking-sidebar {
        position: sticky;
        top: 100px;
        background: var(--card-bg);
        border: 1px solid var(--glass-border);
        padding: 30px;
        border-radius: 16px;
        box-shadow: var(--shadow);
        height: fit-content;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 8px;
        color: var(--text-primary);
    }
    .form-control {
        width: 100%;
        padding: 12px 16px;
        border-radius: 8px;
        border: 1px solid var(--glass-border);
        font-family: inherit;
        font-size: 0.95rem;
        background: var(--input-bg);
        color: var(--text-primary);
        transition: all 0.3s;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--teal, #14B8A6);
        background: var(--card-bg);
        box-shadow: 0 0 0 3px rgba(20,184,166,0.1);
    }

    /* Amenities List */
    .facility-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 12px;
        list-style: none;
        padding: 0;
    }
    .facility-list li {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 0;
    }

    /* Related Section */
    .related-title {
        font-size: 1.8rem;
        font-weight: 800;
        margin-bottom: 30px;
        position: relative;
        padding-bottom: 10px;
    }
    .related-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 3px;
        background: var(--teal, #14B8A6);
        border-radius: 2px;
    }
</style>

<div class="tour-detail-page">
    <div class="container">
        {{-- Breadcrumbs / Back button --}}
        <div style="margin-bottom: 24px;">
            <a href="{{ route('tours.category', $tour->category->slug) }}" style="text-decoration: none; color: var(--teal, #14B8A6); font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                ← Kembali ke Kategori {{ $tour->category->name }}
            </a>
        </div>

        {{-- Title Area --}}
        <div style="margin-bottom: 30px;">
            <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px;">
                <span style="background: rgba(20,184,166,0.1); color: var(--teal, #14B8A6); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                    {{ $tour->category->name }}
                </span>
                @if($tour->location)
                    <span style="background: rgba(15,42,82,0.05); color: var(--text-muted); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 500;">
                        📍 {{ $tour->location }}
                    </span>
                @endif
                @if($tour->duration)
                    <span style="background: rgba(249,115,22,0.1); color: var(--orange, #F97316); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                        ⏳ {{ $tour->duration }}
                    </span>
                @endif
            </div>
            <h1 style="font-size: 2.5rem; font-weight: 850; line-height: 1.2; color: var(--text-primary, #0F2A52);">{{ $tour->title }}</h1>
        </div>

        @if(session('success'))
            <div style="background: #D1FAE5; color: #065F46; padding: 16px; border-radius: 8px; margin-bottom: 30px; font-weight: 500;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: #FEE2E2; color: #991B1B; padding: 16px; border-radius: 8px; margin-bottom: 30px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="tour-container">
            {{-- Main Content Column --}}
            <div>
                {{-- Gallery --}}
                <div class="tour-gallery">
                    <div class="main-image-wrapper">
                        <img id="main-gallery-img" src="{{ $tour->thumbnail ? asset('storage/' . $tour->thumbnail) : asset('images/hero_bg.jpg') }}" alt="{{ $tour->title }}">
                    </div>
                    @if($tour->gallery && count($tour->gallery) > 0)
                        <div class="gallery-thumbs">
                            <div class="thumb-item active" onclick="changeMainImage('{{ $tour->thumbnail ? asset('storage/' . $tour->thumbnail) : asset('images/hero_bg.jpg') }}', this)">
                                <img src="{{ $tour->thumbnail ? asset('storage/' . $tour->thumbnail) : asset('images/hero_bg.jpg') }}" alt="Thumbnail">
                            </div>
                            @foreach($tour->gallery as $galleryImg)
                                <div class="thumb-item" onclick="changeMainImage('{{ asset('storage/' . $galleryImg) }}', this)">
                                    <img src="{{ asset('storage/' . $galleryImg) }}" alt="Thumbnail">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Tabs Container --}}
                <div class="tour-tabs">
                    <div class="tabs-header">
                        <button class="tab-btn active" onclick="switchTab(event, 'deskripsi')">Deskripsi</button>
                        <button class="tab-btn" onclick="switchTab(event, 'itinerary')">Rencana Perjalanan</button>
                        <button class="tab-btn" onclick="switchTab(event, 'fasilitas')">Fasilitas</button>
                        <button class="tab-btn" onclick="switchTab(event, 'ketentuan')">Syarat & Ketentuan</button>
                    </div>

                    {{-- Deskripsi Pane --}}
                    <div id="deskripsi" class="tab-pane active">
                        <h3>Tentang Paket Wisata</h3>
                        <div style="font-size: 1.05rem;">
                            {!! nl2br(e($tour->description)) !!}
                        </div>
                    </div>

                    {{-- Itinerary Pane --}}
                    <div id="itinerary" class="tab-pane">
                        <h3>Rencana Perjalanan (Itinerary)</h3>
                        <div style="font-size: 1.05rem;">
                            {!! nl2br(e($tour->itinerary ?: 'Itinerary tidak didefinisikan untuk paket ini.')) !!}
                        </div>
                    </div>

                    {{-- Fasilitas Pane --}}
                    <div id="fasilitas" class="tab-pane">
                        <h3>Fasilitas yang Didapat</h3>
                        <div style="font-size: 1.05rem;">
                            {!! nl2br(e($tour->facilities ?: 'Informasi fasilitas tidak tersedia.')) !!}
                        </div>
                    </div>

                    {{-- Ketentuan Pane --}}
                    <div id="ketentuan" class="tab-pane">
                        <h3>Syarat & Ketentuan</h3>
                        <div style="font-size: 1.05rem;">
                            {!! nl2br(e($tour->terms ?: 'Ketentuan standar berlaku untuk pemesanan paket ini.')) !!}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Booking Sidebar Column --}}
            <div>
                <div class="booking-sidebar">
                    <div style="margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid rgba(0,0,0,0.06);">
                        <span style="font-size: 0.9rem; color: var(--text-muted); display: block; margin-bottom: 4px;">Harga per Orang</span>
                        <div style="display: flex; align-items: baseline; gap: 8px;">
                            <span style="font-size: 2rem; font-weight: 850; color: var(--orange, #F97316);">Rp {{ number_format($tour->price, 0, ',', '.') }}</span>
                        </div>
                        <span style="font-size:0.8rem; color:var(--text-muted);">DP minimal Rp 100.000 / pax</span>
                    </div>

                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 20px;">Formulir Pemesanan</h3>

                    <form method="POST" action="{{ route('bookings.initiate') }}" id="booking-form">
                        @csrf
                        <input type="hidden" name="tour_id" value="{{ $tour->id }}">

                        <div class="form-group">
                            <label class="form-label" for="name">Nama Lengkap *</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Hubert Blaine..." required value="{{ old('name') }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="whatsapp">No. WhatsApp / HP *</label>
                            <input type="text" name="whatsapp" id="whatsapp" class="form-control" placeholder="081234567890" required value="{{ old('whatsapp') }}">
                            <small style="color:var(--text-muted); font-size:0.8rem;">Gunakan format nomor lokal atau internasional</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Alamat Email *</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="email@domain.com" required value="{{ old('email') }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="departure_date">Tanggal Keberangkatan *</label>
                            <input type="date" name="departure_date" id="departure_date" class="form-control" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ old('departure_date') }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="participants">Jumlah Peserta *</label>
                            <input type="number" name="participants" id="participants" class="form-control" min="1" required value="{{ old('participants', 1) }}" oninput="calculateTotal()">
                        </div>

                        @php
                            $refCode = request()->query('ref') ?? request()->query('referral') ?? session('travine_referral_code') ?? request()->cookie('travine_referral_code');
                            $partnerRef = null;
                            if ($refCode) {
                                $partnerRef = \App\Models\Partner::where('referral_code', strtoupper(trim($refCode)))->where('status', 'active')->first();
                            }
                            $hasPartnerLink = $partnerRef !== null;
                            $referralValue = old('referral_code', $partnerRef ? $partnerRef->referral_code : '');
                        @endphp

                        <div class="form-group">
                            <label class="form-label" for="referral_code">
                                Kode Referral 
                                @if($hasPartnerLink)
                                    <span style="color:var(--teal, #14B8A6); font-size:0.8rem; font-weight:600;">(Mitra Terverifikasi)</span>
                                @else
                                    <span style="color:var(--text-muted); font-size:0.8rem;">(Opsional)</span>
                                @endif
                            </label>
                            <input type="text" 
                                   name="referral_code" 
                                   id="referral_code" 
                                   class="form-control" 
                                   placeholder="Masukkan kode referral jika ada" 
                                   value="{{ $referralValue }}" 
                                   {{ $hasPartnerLink ? 'readonly style=background:#e2e8f0;cursor:not-allowed;font-weight:700;color:#0F2A52;' : '' }}>
                            @if($hasPartnerLink)
                                <small style="color:var(--teal, #14B8A6); font-size:0.825rem; display:block; margin-top:6px; font-weight:500;">
                                    ✓ Terhubung dengan rekomendasi mitra <strong>{{ $partnerRef->name }}</strong> ({{ $partnerRef->referral_code }}). Kode dikunci secara otomatis.
                                </small>
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="notes">Catatan Tambahan (Opsional)</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Permintaan khusus atau kebutuhan khusus lainnya...">{{ old('notes') }}</textarea>
                        </div>

                        {{-- Calculation Box --}}
                        <div id="price-calculator" style="background: rgba(15,42,82,0.03); padding: 16px; border-radius: 8px; margin-bottom: 24px; border: 1px dashed rgba(0,0,0,0.1);">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem;">
                                <span>Harga Tiket</span>
                                <span>Rp {{ number_format($tour->price, 0, ',', '.') }} × <span id="calc-qty">1</span></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.05rem; border-top: 1px solid rgba(0,0,0,0.06); padding-top: 8px; color: var(--text-primary);">
                                <span>Total Biaya</span>
                                <span id="calc-total" style="color:var(--orange, #F97316);">Rp {{ number_format($tour->price, 0, ',', '.') }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.85rem; padding-top: 8px; color: var(--text-muted); border-top: 1px dashed rgba(0,0,0,0.08); margin-top: 8px;">
                                <span>DP Minimal (Rp 100rb/pax)</span>
                                <span id="calc-dp" style="color:#F97316; font-weight:600;">Rp 100.000</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn--primary" style="width: 100%; border: none; padding: 14px; border-radius: 8px; font-size: 1.05rem; font-weight: 700; cursor: pointer; display:flex; align-items:center; justify-content:center; gap:8px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            Lanjut ke Pembayaran
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Related Tours --}}
        @if($relatedTours->count() > 0)
            <div style="margin-top: 80px;">
                <h2 class="related-title">Paket Terkait</h2>
                <div class="tours-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
                    @foreach($relatedTours as $relTour)
                        <div class="tour-card" style="background: var(--card-bg, #ffffff); border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: transform 0.3s ease, box-shadow 0.3s ease; display: flex; flex-direction: column; height: 100%;">
                            <div class="tour-card__image-wrapper" style="position: relative; height: 220px; overflow: hidden;">
                                <img src="{{ $relTour->thumbnail ? asset('storage/' . $relTour->thumbnail) : asset('images/hero_bg.jpg') }}" alt="{{ $relTour->title }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                                @if($relTour->duration)
                                    <span class="tour-card__duration-badge" style="position: absolute; bottom: 20px; right: 20px; background: rgba(15,42,82,0.85); color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; backdrop-filter: blur(4px);">
                                        ⏳ {{ $relTour->duration }}
                                    </span>
                                @endif
                            </div>
                            <div class="tour-card__body" style="padding: 24px; display: flex; flex-direction: column; flex-grow: 1;">
                                <h3 class="tour-card__title" style="font-size: 1.2rem; font-weight: 700; margin-bottom: 12px; line-height: 1.4; color: var(--text-primary, #0F2A52);">
                                    <a href="{{ route('tours.show', $relTour->slug) }}" style="color: inherit; text-decoration: none; transition: color 0.3s;">
                                        {{ $relTour->title }}
                                    </a>
                                </h3>
                                <p class="tour-card__desc" style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6; margin-bottom: 20px; flex-grow: 1;">
                                    {{ Str::limit(strip_tags($relTour->short_description), 80) }}
                                </p>
                                <div class="tour-card__footer" style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(0,0,0,0.06); padding-top: 16px; margin-top: auto;">
                                    <div>
                                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">Mulai dari</span>
                                        <span style="font-size: 1.15rem; font-weight: 800; color: var(--orange, #F97316);">Rp {{ number_format($relTour->price, 0, ',', '.') }}</span>
                                    </div>
                                    <a href="{{ route('tours.show', $relTour->slug) }}" class="btn btn--primary" style="text-decoration: none; padding: 8px 16px; font-size: 0.85rem; font-weight: 600; border-radius: 8px;">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    // Gallery switching
    function changeMainImage(src, element) {
        document.getElementById('main-gallery-img').src = src;
        // update active state
        const thumbs = document.querySelectorAll('.thumb-item');
        thumbs.forEach(t => t.classList.remove('active'));
        element.classList.add('active');
    }

    // Tabs switching
    function switchTab(event, tabId) {
        // Remove active class from buttons
        const buttons = document.querySelectorAll('.tab-btn');
        buttons.forEach(btn => btn.classList.remove('active'));
        
        // Remove active class from panes
        const panes = document.querySelectorAll('.tab-pane');
        panes.forEach(pane => pane.classList.remove('active'));
        
        // Set current as active
        event.currentTarget.classList.add('active');
        document.getElementById(tabId).classList.add('active');
    }

    // Live price calculator
    const basePrice = {{ $tour->price }};
    const dpPerPax  = 100000;
    function calculateTotal() {
        const input = document.getElementById('participants');
        let qty = parseInt(input.value);
        if (isNaN(qty) || qty < 1) qty = 1;
        
        const total = basePrice * qty;
        const dp    = dpPerPax * qty;
        
        document.getElementById('calc-qty').innerText = qty;
        document.getElementById('calc-total').innerText = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('calc-dp').innerText    = 'Rp ' + dp.toLocaleString('id-ID');
    }
</script>
@endsection
