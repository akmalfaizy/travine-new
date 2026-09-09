<div class="partner-dash__nav" style="display:flex; gap:12px; margin-bottom:24px; border-bottom:1px solid var(--border-color); padding-bottom:12px; flex-wrap:wrap;">
    <a href="{{ route('partner.dashboard') }}" class="btn {{ request()->routeIs('partner.dashboard') ? 'btn--primary' : 'btn--outline' }}" style="padding:8px 16px; font-size:0.85rem;">
        📊 Dashboard
    </a>
    <a href="{{ route('partner.profile') }}" class="btn {{ request()->routeIs('partner.profile') ? 'btn--primary' : 'btn--outline' }}" style="padding:8px 16px; font-size:0.85rem;">
        👤 Profil & Rekening Bank
    </a>
    <a href="{{ route('partner.withdrawals') }}" class="btn {{ request()->routeIs('partner.withdrawals') ? 'btn--primary' : 'btn--outline' }}" style="padding:8px 16px; font-size:0.85rem;">
        💸 Penarikan Komisi
    </a>
    <a href="{{ route('partner.referrals') }}" class="btn {{ request()->routeIs('partner.referrals') ? 'btn--primary' : 'btn--outline' }}" style="padding:8px 16px; font-size:0.85rem;">
        🔗 Link Referral Destinasi
    </a>
</div>
