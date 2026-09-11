{{-- Footer --}}
<footer class="footer" id="footer">
    <div class="container">
        <div class="footer__grid">
            {{-- Brand --}}
            <div class="footer__brand">
                <a href="#hero" class="navbar__logo">
                    <img src="{{ asset('images/logo_tagline.png') }}" alt="travine.id" class="navbar__logo-img">
                </a>
                <p class="footer__tagline">
                    {{ __('The best, safe, comfortable, and trusted travel solution for your dream vacation.') }}
                </p>
            </div>

            {{-- Quick Links --}}
            <div class="footer__links">
                <h4 class="footer__heading">{{ __('Quick Links') }}</h4>
                <ul>
                    <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
                    <li><a href="{{ route('home') }}#about">{{ __('About Us') }}</a></li>
                    <li><a href="{{ route('home') }}#services">{{ __('Services') }}</a></li>
                    <li><a href="{{ route('home') }}#portfolio">{{ __('Portfolio') }}</a></li>
                    <li><a href="{{ route('home') }}#contact">{{ __('Contact Us') }}</a></li>
                </ul>
            </div>

            {{-- Services --}}
            <div class="footer__links">
                <h4 class="footer__heading">{{ __('Services') }}</h4>
                <ul>
                    <li><a href="#services">{{ __('Paket Wisata') }}</a></li>
                    <li><a href="#services">{{ __('Pengembangan Ekosistem') }}</a></li>
                    <li><a href="#services">{{ __('Konsultan Destinasi') }}</a></li>
                    <li><a href="#services">{{ __('Destination Branding') }}</a></li>
                </ul>
            </div>

            {{-- Contact --}}
            <div class="footer__links">
                <h4 class="footer__heading">{{ __('Contact Us') }}</h4>
                <ul>
                    <li>travineid@gmail.com</li>
                    <li>0821-3792-5585</li>
                    <li>Yogyakarta, Indonesia</li>
                </ul>
                <div class="footer__socials">
                    <a href="#" aria-label="Instagram">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </a>
                    <a href="#" aria-label="LinkedIn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                    </a>
                    <a href="#" aria-label="Facebook">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                    <a href="#" aria-label="YouTube">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19.13C5.12 19.56 12 19.56 12 19.56s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.43z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="footer__bottom">
            <p>&copy; {{ date('Y') }} travine.id. {{ __('All rights reserved.') }}</p>
            <p>{{ __('Transforming Tourism with Authentic Experiences') }}</p>
        </div>
    </div>
</footer>
