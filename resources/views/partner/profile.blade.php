@extends('layouts.app')

@section('content')
<section class="section" style="padding-top:120px;min-height:100vh;">
    <div class="container">
        {{-- Header --}}
        <div class="partner-dash__header">
            <div>
                <h1 class="partner-dash__title">Profil & Data Rekening Bank</h1>
                <p class="partner-dash__subtitle">Kelola informasi data diri dan rekening untuk pencairan komisi Anda.</p>
            </div>
            <div class="partner-dash__actions">
                <a href="{{ route('partner.dashboard') }}" class="btn btn--outline" style="font-size:0.85rem;padding:10px 20px;">← Dashboard</a>
            </div>
        </div>

        @include('partner.nav')

        @if(session('success'))
            <div style="background:rgba(20,184,166,0.15); border:1px solid rgba(20,184,166,0.3); color:var(--teal); padding:14px 20px; border-radius:10px; margin-bottom:24px; font-weight:600;">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background:rgba(248,113,113,0.15); border:1px solid rgba(248,113,113,0.3); color:#f87171; padding:14px 20px; border-radius:10px; margin-bottom:24px; font-weight:600;">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        @if(!$partner->isProfileComplete())
            <div style="background:rgba(249,115,22,0.15); border:1px solid rgba(249,115,22,0.3); color:var(--orange); padding:16px 20px; border-radius:12px; margin-bottom:24px;">
                💡 <strong>Perhatian:</strong> Rekening bank Anda belum lengkap. Mohon isi data rekening bank di bawah ini agar Anda dapat mengajukan pencairan komisi!
            </div>
        @endif

        <div class="partner-grid-2">
            {{-- Form 1: Data Diri --}}
            <div class="partner-auth-card" style="padding:32px;">
                <h3 style="font-size:1.2rem; font-weight:700; color:var(--text-primary); margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                    👤 Data Pribadi Partner
                </h3>

                <form method="POST" action="{{ route('partner.profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600; color:var(--text-primary);">Nama Lengkap *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $partner->name) }}" required>
                    </div>

                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600; color:var(--text-primary);">Alamat Email (Terkunci)</label>
                        <input type="email" class="form-control" value="{{ $partner->email }}" readonly>
                    </div>

                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600; color:var(--text-primary);">No. WhatsApp / HP *</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $partner->phone) }}" required>
                    </div>

                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600; color:var(--text-primary);">Alamat Tempat Tinggal</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $partner->address) }}</textarea>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:20px;">
                        <div class="form-group">
                            <label class="form-label" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600; color:var(--text-primary);">Kota / Kabupaten</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $partner->city) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600; color:var(--text-primary);">Provinsi</label>
                            <input type="text" name="province" class="form-control" value="{{ old('province', $partner->province) }}">
                        </div>
                    </div>

                    <button type="submit" class="btn btn--primary" style="width:100%; padding:12px; border-radius:8px; font-weight:700;">
                        💾 Simpan Profil
                    </button>
                </form>
            </div>

            {{-- Form 2: Rekening Bank --}}
            <div class="partner-auth-card" style="padding:32px;">
                <h3 style="font-size:1.2rem; font-weight:700; color:var(--text-primary); margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                    🏦 Rekening Bank Pembayaran Komisi
                </h3>
                <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:20px; line-height:1.5;">
                    Data ini digunakan oleh tim Finance travine.id untuk menyalurkan pencairan komisi Anda secara tepat dan aman.
                </p>

                <form method="POST" action="{{ route('partner.bank.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600; color:var(--text-primary);">Nama Bank *</label>
                        <input type="text" name="bank_name" class="form-control" placeholder="Contoh: BCA, Mandiri, BRI, BNI" value="{{ old('bank_name', $partner->bank_name) }}" required>
                    </div>

                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600; color:var(--text-primary);">Nomor Rekening Bank *</label>
                        <input type="text" name="bank_account_number" class="form-control" placeholder="Contoh: 1234567890" value="{{ old('bank_account_number', $partner->bank_account_number) }}" required>
                    </div>

                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600; color:var(--text-primary);">Nama Pemilik Rekening *</label>
                        <input type="text" name="bank_account_name" class="form-control" placeholder="Sesuai nama di buku tabungan" value="{{ old('bank_account_name', $partner->bank_account_name) }}" required>
                        <small style="color:var(--text-muted); font-size:0.78rem; display:block; margin-top:4px;">Pastikan nama di rekening sesuai dengan identitas Anda.</small>
                    </div>

                    <button type="submit" class="btn btn--primary" style="width:100%; padding:12px; border-radius:8px; font-weight:700;">
                        🏦 Simpan Data Rekening Bank
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
