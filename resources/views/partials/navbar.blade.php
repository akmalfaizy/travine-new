{{-- Navbar --}}
<nav class="navbar" id="navbar">
    <div class="container navbar__inner">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="navbar__logo">
            <img src="{{ asset('images/logo_tagline.png') }}" alt="travine.id" class="navbar__logo-img">
        </a>

        {{-- Desktop Navigation --}}
        <ul class="navbar__menu" id="navMenu">
            <li><a href="{{ route('home') }}" class="navbar__link {{ request()->routeIs('home') ? 'active' : '' }}">{{ __('Home') }}</a></li>

            {{-- Dynamic Wisata Dropdown --}}
            <li class="navbar__dropdown">
                <a href="#" class="navbar__link navbar__link--dropdown">
                    {{ __('Services') }}
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left:4px; vertical-align:middle;"><polyline points="6 9 12 15 18 9"/></svg>
                </a>
                <div class="navbar__dropdown-menu">
                    @foreach($navCategories as $cat)
                    <a href="{{ route('tours.category', $cat->slug) }}" class="navbar__dropdown-item">
                        <span style="font-size:1.1rem;">{{ $cat->icon }}</span>
                        <div>
                            <div style="font-weight:600; font-size:0.9rem;">{{ $cat->name }}</div>
                            <div style="font-size:0.75rem; opacity:0.6;">{{ $cat->active_tours_count }} {{ __('packages available') }}</div>
                        </div>
                    </a>
                    @endforeach
                    @if($navCategories->isEmpty())
                    <div class="navbar__dropdown-item" style="color:var(--text-muted); font-size:0.85rem;">{{ __('Coming soon...') }}</div>
                    @endif
                </div>
            </li>

            <li><a href="{{ route('news.index') }}" class="navbar__link {{ request()->routeIs('news.*') ? 'active' : '' }}">{{ __('News') }}</a></li>
            <li><a href="{{ route('home') }}#portfolio" class="navbar__link">{{ __('Portfolio') }}</a></li>

            {{-- Language Switcher --}}
            <li class="navbar__dropdown" id="lang-switcher">
                <a href="#" class="navbar__link navbar__link--dropdown" style="gap:6px; display:flex; align-items:center;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    <span style="font-weight:600; font-size:0.85rem; text-transform:uppercase;">{{ app()->getLocale() }}</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </a>
                <div class="navbar__dropdown-menu" style="min-width:130px;">
                    <a href="{{ route('lang.switch', 'id') }}" class="navbar__dropdown-item {{ app()->getLocale() === 'id' ? 'navbar__dropdown-item--active' : '' }}" style="gap:10px; align-items:center;">
                        <span style="font-size:1.1rem;">🇮🇩</span>
                        <div>
                            <div style="font-weight:600; font-size:0.9rem;">Indonesia</div>
                        </div>
                        @if(app()->getLocale() === 'id')
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#14B8A6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="margin-left:auto;"><polyline points="20 6 9 17 4 12"/></svg>
                        @endif
                    </a>
                    <a href="{{ route('lang.switch', 'en') }}" class="navbar__dropdown-item {{ app()->getLocale() === 'en' ? 'navbar__dropdown-item--active' : '' }}" style="gap:10px; align-items:center;">
                        <span style="font-size:1.1rem;">🇬🇧</span>
                        <div>
                            <div style="font-weight:600; font-size:0.9rem;">English</div>
                        </div>
                        @if(app()->getLocale() === 'en')
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#14B8A6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="margin-left:auto;"><polyline points="20 6 9 17 4 12"/></svg>
                        @endif
                    </a>
                </div>
            </li>

            <li>
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle Theme">
                    <svg class="theme-toggle__icon theme-toggle__icon--moon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    <svg class="theme-toggle__icon theme-toggle__icon--sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                </button>
            </li>

            <li>
                @if(Auth::guard('partner')->check())
                    <a href="{{ route('partner.dashboard') }}" class="btn btn--primary" style="padding:8px 18px; font-size:0.85rem; font-weight:700; display:inline-flex; align-items:center; gap:6px; text-decoration:none; border-radius:8px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Dashboard {{ __('Mitra Travel') }}
                    </a>
                @else
                    <a href="{{ route('partner.login') }}" class="btn btn--primary" style="padding:8px 18px; font-size:0.85rem; font-weight:700; display:inline-flex; align-items:center; gap:6px; text-decoration:none; border-radius:8px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                        {{ __('Login Mitra') }}
                    </a>
                @endif
            </li>
        </ul>

        {{-- Hamburger --}}
        <button class="navbar__hamburger" id="navHamburger" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>
