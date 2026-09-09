@extends('layouts.app')

@section('content')
<section class="section" style="padding-top:120px;min-height:100vh;">
    <div class="container">
        {{-- Header --}}
        <div class="partner-dash__header">
            <div>
                <h1 class="partner-dash__title">Link Referral Destinasi & Paket Wisata</h1>
                <p class="partner-dash__subtitle">Salin dan bagikan link referral khusus paket wisata untuk calon customer Anda.</p>
            </div>
            <div class="partner-dash__actions">
                <a href="{{ route('partner.dashboard') }}" class="btn btn--outline" style="font-size:0.85rem;padding:10px 20px;">← Dashboard</a>
            </div>
        </div>

        @include('partner.nav')

        {{-- Main Referral Link Card --}}
        <div class="partner-dash__referral" style="margin-bottom:30px;">
            <span class="partner-dash__referral-label">🔗 Link Referral Utama Website:</span>
            <div class="partner-dash__referral-box" style="margin-top:8px;">
                <code id="main-ref-link">{{ $partner->referral_link }}</code>
                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('main-ref-link').textContent);this.textContent='✓ Copied!';setTimeout(()=>this.textContent='Copy Link',2000)" class="btn btn--primary" style="padding:8px 16px;font-size:0.8rem;">Copy Link</button>
            </div>
        </div>

        {{-- Tour Specific Referral Links --}}
        <div class="partner-auth-card" style="padding:32px;">
            <h3 style="font-size:1.2rem; font-weight:700; color:var(--text-primary); margin-bottom:20px;">
                🌴 Link Referral Per Paket Wisata
            </h3>

            @if(isset($tours) && $tours->count() > 0)
                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(340px, 1fr)); gap:20px;">
                    @foreach($tours as $t)
                        @php
                            $tourRefUrl = route('tours.show', $t->slug) . '?ref=' . $partner->referral_code;
                            $estComm = $t->estimated_commission;
                        @endphp
                        <div style="background:var(--bg-alt); border:1px solid var(--border-color); border-radius:12px; padding:20px; display:flex; flex-direction:column; justify-content:space-between;">
                            <div>
                                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                                    <span style="background:rgba(20,184,166,0.15); color:var(--teal); font-size:0.75rem; font-weight:700; padding:2px 10px; border-radius:12px;">
                                        {{ $t->category?->name }}
                                    </span>
                                    <span style="color:var(--orange,#F97316); font-weight:800; font-size:0.9rem;">
                                        Komisi {{ $t->commission_percentage }}%
                                    </span>
                                </div>
                                <h4 style="font-size:1.05rem; font-weight:700; color:var(--text-primary); margin-bottom:8px; line-height:1.4;">
                                    {{ $t->title }}
                                </h4>
                                <div style="font-size:0.85rem; color:var(--text-muted); margin-bottom:12px;">
                                    Harga: <strong style="color:var(--text-primary);">{{ $t->price_formatted }}</strong> / pax
                                    @if($estComm > 0)
                                        <span style="display:block; color:var(--teal); font-size:0.8rem; margin-top:2px;">Est. Komisi: Rp {{ number_format($estComm, 0, ',', '.') }}/pax</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div style="background:var(--bg-offset); border:1px solid var(--border-color); padding:8px 12px; border-radius:6px; font-family:monospace; font-size:0.78rem; color:var(--teal); overflow-x:auto; margin-bottom:10px; white-space:nowrap;">
                                    <span id="tour-link-{{ $t->id }}">{{ $tourRefUrl }}</span>
                                </div>
                                <button type="button" 
                                        onclick="navigator.clipboard.writeText(document.getElementById('tour-link-{{ $t->id }}').textContent);this.textContent='✓ Link Copied!';setTimeout(()=>this.textContent='Copy Referral Link',2000)" 
                                        class="btn btn--primary" 
                                        style="width:100%; padding:8px; font-size:0.8rem; font-weight:600; border-radius:6px;">
                                    Copy Referral Link
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align:center; padding:40px; color:var(--text-muted);">
                    Belum ada paket wisata aktif.
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
