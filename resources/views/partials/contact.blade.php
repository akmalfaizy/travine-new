{{-- Contact Section --}}
<section class="contact section" id="contact">
    <div class="container">
        <div class="section__header reveal">
            <span class="section__badge">Hubungi Kami</span>
            <h2 class="section__title">Get in <span class="text-gradient">Touch</span></h2>
            <p class="section__subtitle">Siap membantu mewujudkan proyek wisata impian Anda. Hubungi kami sekarang.</p>
        </div>

        <div class="contact__grid">
            {{-- Contact Form --}}
            <div class="contact__form-wrapper reveal" id="contact-form-wrapper">
                <form class="contact__form" id="contactForm">
                    @csrf
                    <div class="contact__form-group">
                        <label for="contact-name" class="contact__label">Nama Lengkap</label>
                        <input type="text" id="contact-name" name="name" class="contact__input" placeholder="Masukkan nama Anda" required>
                    </div>
                    <div class="contact__form-group">
                        <label for="contact-email" class="contact__label">Email</label>
                        <input type="email" id="contact-email" name="email" class="contact__input" placeholder="email@contoh.com" required>
                    </div>
                    <div class="contact__form-group">
                        <label for="contact-subject" class="contact__label">Subjek</label>
                        <input type="text" id="contact-subject" name="subject" class="contact__input" placeholder="Subjek pesan Anda">
                    </div>
                    <div class="contact__form-group">
                        <label for="contact-message" class="contact__label">Pesan</label>
                        <textarea id="contact-message" name="message" class="contact__input contact__textarea" placeholder="Ceritakan kebutuhan wisata Anda..." rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn--primary btn--full" id="contact-submit">
                        <span>Kirim Pesan</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    </button>
                </form>
                {{-- Success Message --}}
                <div class="contact__success" id="contactSuccess" style="display:none;">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#14B8A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <h3>Pesan Terkirim!</h3>
                    <p>Terima kasih telah menghubungi kami. Tim kami akan segera merespons pesan Anda.</p>
                </div>
            </div>

            {{-- Contact Info --}}
            <div class="contact__info reveal reveal--delay-1">
                <div class="contact__info-card">
                    <div class="contact__info-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <div>
                        <h4>Email</h4>
                        <p>travineid@gmail.com</p>
                    </div>
                </div>

                <div class="contact__info-card">
                    <div class="contact__info-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </div>
                    <div>
                        <h4>WhatsApp</h4>
                        <p>0821-3792-5585</p>
                    </div>
                </div>

                <div class="contact__info-card">
                    <div class="contact__info-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div>
                        <h4>Lokasi</h4>
                        <p>Yogyakarta, Indonesia</p>
                    </div>
                </div>

                <div class="contact__info-card">
                    <div class="contact__info-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <h4>Jam Operasional</h4>
                        <p>Setiap Hari</p>
                    </div>
                </div>

                {{-- Social Media --}}
                <div class="contact__socials">
                    <h4>Follow Us</h4>
                    <div class="contact__social-links">
                        <a href="https://www.instagram.com/travine.id/" class="contact__social-link" aria-label="Instagram" id="social-instagram">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        </a>
                        <!-- <a href="#" class="contact__social-link" aria-label="LinkedIn" id="social-linkedin">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                        </a>
                        <a href="#" class="contact__social-link" aria-label="Facebook" id="social-facebook">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                        </a> -->
                        <a href="https://www.youtube.com/@travineid" class="contact__social-link" aria-label="YouTube" id="social-youtube">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19.13C5.12 19.56 12 19.56 12 19.56s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.43z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
