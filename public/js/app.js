document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initNavbar();
    initRevealAnimations();
    initSmoothScroll();
    initContactForm();
    initParallax();
});

// ===== THEME =====
function initTheme() {
    const toggle = document.getElementById('themeToggle');
    const storedTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
    
    if (storedTheme) {
        document.documentElement.setAttribute('data-theme', storedTheme);
    }

    toggle.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const targetTheme = currentTheme === 'light' ? 'dark' : 'light';
        
        document.documentElement.setAttribute('data-theme', targetTheme);
        localStorage.setItem('theme', targetTheme);
    });
}

function initNavbar() {
    const navbar = document.getElementById('navbar');
    const hamburger = document.getElementById('navHamburger');
    const menu = document.getElementById('navMenu');
    const links = document.querySelectorAll('.navbar__link');

    // Scroll effect
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 50);
    });

    // Hamburger toggle
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        menu.classList.toggle('active');
        document.body.style.overflow = menu.classList.contains('active') ? 'hidden' : '';
    });

    // Close menu on link click
    links.forEach(link => {
        link.addEventListener('click', (e) => {
            if (link.classList.contains('navbar__link--dropdown')) {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    link.closest('.navbar__dropdown').classList.toggle('active');
                }
                return;
            }
            hamburger.classList.remove('active');
            menu.classList.remove('active');
            document.body.style.overflow = '';
        });
    });

    // Active link on scroll
    const sections = document.querySelectorAll('section[id]');
    window.addEventListener('scroll', () => {
        const scrollY = window.scrollY + 100;
        sections.forEach(section => {
            const top = section.offsetTop;
            const height = section.offsetHeight;
            const id = section.getAttribute('id');
            const link = document.querySelector(`.navbar__link[href="#${id}"]`);
            if (link) {
                link.classList.toggle('active', scrollY >= top && scrollY < top + height);
            }
        });
    });
}

// ===== REVEAL ON SCROLL =====
function initRevealAnimations() {
    const reveals = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

    reveals.forEach(el => observer.observe(el));
}

// ===== SMOOTH SCROLL =====
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
}

// ===== CONTACT FORM =====
function initContactForm() {
    const form = document.getElementById('contactForm');
    const success = document.getElementById('contactSuccess');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<span>Mengirim...</span>';
        btn.disabled = true;

        const formData = new FormData(form);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                       || formData.get('_token');

        try {
            const response = await fetch('/contact', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json();

            if (response.ok && data.success) {
                form.style.display = 'none';
                success.style.display = 'block';
            } else {
                const errors = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'Terjadi kesalahan, coba lagi.');
                alert(errors);
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            }
        } catch (err) {
            alert('Gagal mengirim pesan. Periksa koneksi internet Anda.');
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }
    });
}

// ===== PARALLAX =====
function initParallax() {
    const shapes = document.querySelectorAll('.hero__shape');
    window.addEventListener('scroll', () => {
        const scrolled = window.scrollY;
        shapes.forEach((shape, i) => {
            const speed = 0.03 + (i * 0.015);
            shape.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });
}

// ===== PORTFOLIO MODAL =====
function openPortfolioModal(title, category, content, imageUrl) {
    const modal = document.getElementById('portfolioModal');
    
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalCategory').textContent = category;
    document.getElementById('modalContent').innerHTML = content.replace(/\n/g, '<br>');
    document.getElementById('modalImage').src = imageUrl;
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closePortfolioModal() {
    const modal = document.getElementById('portfolioModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

// Close modal on escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closePortfolioModal();
});
