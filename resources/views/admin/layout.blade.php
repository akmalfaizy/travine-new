<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Admin travine.id</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="icon" href="{{ asset('images/favicon_travine.svg') }}" type="image/svg+xml">
    <style>
        :root { --sidebar-width: 260px; }
        body { background: var(--bg-alt); display: flex; min-height: 100vh; }
        /* Sidebar */
        .sidebar { width: var(--sidebar-width); background: var(--bg-main); border-right: 1px solid var(--glass-border); padding: 24px 16px; display: flex; flex-direction: column; gap: 8px; position: fixed; height: 100vh; overflow-y: auto; z-index: 100; }
        .sidebar-logo { display: flex; align-items: center; gap: 10px; padding: 8px 12px 20px; border-bottom: 1px solid var(--glass-border); margin-bottom: 8px; }
        .sidebar-logo img { height: 32px; }
        .sidebar-section { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); padding: 12px 12px 4px; }
        .admin-nav-link { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: var(--radius-sm); color: var(--text-secondary); font-weight: 500; font-size: 0.9rem; transition: var(--transition); text-decoration: none; }
        .admin-nav-link:hover, .admin-nav-link.active { background: rgba(20,184,166,0.12); color: var(--teal); }
        .admin-nav-link svg { flex-shrink: 0; opacity: 0.7; }
        .admin-nav-link.active svg, .admin-nav-link:hover svg { opacity: 1; }
        .sidebar-bottom { margin-top: auto; padding-top: 16px; border-top: 1px solid var(--glass-border); }
        /* Main */
        .admin-main { margin-left: var(--sidebar-width); flex: 1; padding: 32px 36px; }
        .admin-topbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; }
        .admin-topbar h1 { font-size: 1.4rem; font-weight: 800; color: var(--text-primary); }
        /* Cards */
        .admin-card { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); padding: 28px; backdrop-filter: blur(10px); }
        /* Table */
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th { text-align: left; padding: 12px 16px; border-bottom: 1px solid var(--glass-border); color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.06em; font-weight: 600; }
        .admin-table td { padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: middle; font-size: 0.9rem; }
        .admin-table tr:hover td { background: rgba(255,255,255,0.02); }
        /* Forms */
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.875rem; color: var(--text-secondary); }
        .form-control { width: 100%; padding: 11px 14px; border-radius: var(--radius-sm); background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: var(--text-primary); transition: var(--transition); box-sizing: border-box; font-size: 0.9rem; }
        .form-control:focus { border-color: var(--teal); outline: none; box-shadow: 0 0 0 3px rgba(20,184,166,0.12); }
        select.form-control option { background: #0f1a2e; }
        textarea.form-control { min-height: 120px; resize: vertical; }
        /* Buttons */
        .admin-btn { padding: 9px 18px; border-radius: var(--radius-sm); font-weight: 600; font-size: 0.875rem; cursor: pointer; transition: var(--transition); display: inline-flex; align-items: center; gap: 6px; border: none; text-decoration: none; }
        .admin-btn--primary { background: var(--teal); color: #fff; }
        .admin-btn--primary:hover { background: #0d9488; transform: translateY(-1px); box-shadow: 0 4px 15px rgba(20,184,166,0.3); }
        .admin-btn--secondary { background: rgba(255,255,255,0.07); color: var(--text-secondary); border: 1px solid var(--glass-border); }
        .admin-btn--secondary:hover { background: rgba(255,255,255,0.1); color: var(--text-primary); }
        .admin-btn--danger { background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid rgba(239,68,68,0.2); }
        .admin-btn--danger:hover { background: #ef4444; color: #fff; }
        .admin-btn--warning { background: rgba(251,191,36,0.1); color: #fbbf24; border: 1px solid rgba(251,191,36,0.2); }
        .admin-btn--warning:hover { background: #fbbf24; color: #000; }
        .admin-btn--sm { padding: 5px 12px; font-size: 0.8rem; }
        /* Badges */
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: rgba(34,197,94,0.15); color: #22c55e; }
        .badge-warning { background: rgba(251,191,36,0.15); color: #fbbf24; }
        .badge-danger  { background: rgba(239,68,68,0.15); color: #ef4444; }
        .badge-info    { background: rgba(59,130,246,0.15); color: #60a5fa; }
        .badge-muted   { background: rgba(255,255,255,0.07); color: var(--text-muted); }
        /* Alert */
        .admin-alert { padding: 14px 18px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 0.9rem; font-weight: 500; }
        .admin-alert--success { background: rgba(20,184,166,0.12); border: 1px solid rgba(20,184,166,0.3); color: var(--teal); }
        .admin-alert--error   { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); color: #ef4444; }
        /* Grid */
        .admin-grid { display: grid; gap: 20px; }
        .admin-grid--2 { grid-template-columns: repeat(2,1fr); }
        .admin-grid--3 { grid-template-columns: repeat(3,1fr); }
        .admin-grid--4 { grid-template-columns: repeat(4,1fr); }
        /* Stat cards */
        .stat-card { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); padding: 22px 24px; display: flex; align-items: center; gap: 16px; }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
        .stat-value { font-size: 1.6rem; font-weight: 800; color: var(--text-primary); line-height: 1; }
        .stat-label { font-size: 0.8rem; color: var(--text-muted); margin-top: 4px; }
        /* Pagination */
        .pagination { display: flex; gap: 6px; margin-top: 20px; flex-wrap: wrap; }
        .pagination a, .pagination span { padding: 7px 13px; border-radius: var(--radius-sm); font-size: 0.85rem; background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-secondary); text-decoration: none; }
        .pagination .active span { background: var(--teal); color: #fff; border-color: var(--teal); }
        /* Responsive */
        @media (max-width: 900px) {
            .sidebar { width: 70px; padding: 16px 8px; }
            .sidebar-logo span, .sidebar-section, .admin-nav-link span, .sidebar-bottom .admin-btn span { display: none; }
            .admin-main { margin-left: 70px; padding: 20px; }
            .admin-grid--4 { grid-template-columns: repeat(2,1fr); }
        }
    </style>
    @stack('styles')
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('images/logo_tagline.png') }}" alt="travine.id">
        </div>

        <div class="sidebar-section">Menu Utama</div>
        <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.insights.index') }}" class="admin-nav-link {{ request()->routeIs('admin.insights.*') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            <span>Insight Pengunjung</span>
        </a>

        <div class="sidebar-section">Wisata</div>
        <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            <span>Kategori</span>
        </a>
        <a href="{{ route('admin.tours.index') }}" class="admin-nav-link {{ request()->routeIs('admin.tours.*') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            <span>Paket Wisata</span>
        </a>

        <div class="sidebar-section">Pemesanan</div>
        <a href="{{ route('admin.bookings.index') }}" class="admin-nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            <span>Pemesanan</span>
        </a>

        <div class="sidebar-section">Project & Offering</div>
        <a href="{{ route('admin.offerings.index') }}" class="admin-nav-link {{ request()->routeIs('admin.offerings.*') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
            <span>Offering</span>
        </a>
        <a href="{{ route('admin.project-invoices.index') }}" class="admin-nav-link {{ request()->routeIs('admin.project-invoices.*') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            <span>Project Invoice</span>
        </a>
        <a href="{{ route('admin.vendors.index') }}" class="admin-nav-link {{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span>Data Vendor</span>
        </a>


        <div class="sidebar-section">Kepartneran</div>
        <a href="{{ route('admin.partners.index') }}" class="admin-nav-link {{ request()->routeIs('admin.partners.*') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span>Data Partner</span>
        </a>
        <a href="{{ route('admin.withdrawals.index') }}" class="admin-nav-link {{ request()->routeIs('admin.withdrawals.*') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            <span>Pengajuan Withdraw</span>
        </a>

        <div class="sidebar-section">Konten</div>
        <a href="{{ route('admin.banners.index') }}" class="admin-nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            <span>Banner Slider</span>
        </a>
        <a href="{{ route('admin.news.index') }}" class="admin-nav-link {{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            <span>Berita</span>
        </a>
        <a href="{{ route('admin.portfolios.index') }}" class="admin-nav-link {{ request()->routeIs('admin.portfolios.*') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            <span>Portofolio</span>
        </a>

        <div class="sidebar-section">Sistem</div>
        <a href="{{ route('admin.social-links.edit') }}" class="admin-nav-link {{ request()->routeIs('admin.social-links.*') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2z"/></svg>
            <span>Media Sosial</span>
        </a>
        <a href="{{ route('admin.settings.index') }}" class="admin-nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            <span>Pengaturan</span>
        </a>

        <div class="sidebar-section">Keuangan</div>
        <a href="{{ route('admin.expenditures.index') }}"
           class="admin-nav-link {{ request()->routeIs('admin.expenditures.*') ? 'active' : '' }}"
           style="{{ request()->routeIs('admin.expenditures.*') ? '' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            <span style="display:flex;align-items:center;gap:6px;width:100%;">
                Keuangan
                <span style="margin-left:auto;font-size:0.65rem;background:rgba(251,191,36,0.15);color:#fbbf24;border:1px solid rgba(251,191,36,0.25);border-radius:8px;padding:1px 6px;letter-spacing:0.05em;font-weight:700;">🔐</span>
            </span>
        </a>

        <div class="sidebar-bottom">
            <a href="/" target="_blank" class="admin-btn admin-btn--secondary" style="width:100%; justify-content: center; margin-bottom:8px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                <span>Lihat Website</span>
            </a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="admin-btn admin-btn--danger" style="width:100%; justify-content: center;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="admin-main">
        @if(session('success'))
            <div class="admin-alert admin-alert--success">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="admin-alert admin-alert--error">✗ {{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="admin-alert admin-alert--error">
                @foreach($errors->all() as $error) <div>• {{ $error }}</div> @endforeach
            </div>
        @endif

        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
