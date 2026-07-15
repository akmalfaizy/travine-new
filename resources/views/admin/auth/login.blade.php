<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — travine.id</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="icon" href="{{ asset('images/favicon_travine.svg') }}" type="image/svg+xml">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; background: var(--bg-main); position: relative; overflow: hidden; }
        body::before { content: ''; position: absolute; inset: 0; background: radial-gradient(ellipse 70% 60% at 30% 40%, rgba(20,184,166,0.12) 0%, transparent 60%), radial-gradient(ellipse 50% 50% at 70% 60%, rgba(15,42,82,0.4) 0%, transparent 70%); pointer-events: none; }
        .login-card { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); padding: 48px 40px; width: 100%; max-width: 440px; backdrop-filter: blur(20px); position: relative; z-index: 1; }
        .login-logo { display: flex; align-items: center; gap: 12px; justify-content: center; margin-bottom: 8px; }
        .login-logo img { height: 40px; }
        .login-title { text-align: center; font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin-bottom: 4px; }
        .login-sub { text-align: center; color: var(--text-muted); font-size: 0.9rem; margin-bottom: 32px; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.875rem; color: var(--text-secondary); }
        .form-control { width: 100%; padding: 13px 16px; border-radius: var(--radius-sm); background: var(--input-bg, rgba(255,255,255,0.05)); border: 1px solid var(--glass-border); color: var(--text-primary); font-size: 0.95rem; transition: var(--transition); box-sizing: border-box; }
        .form-control:focus { border-color: var(--teal); outline: none; box-shadow: 0 0 0 3px rgba(20,184,166,0.15); }
        .btn-login { width: 100%; padding: 14px; background: linear-gradient(135deg, var(--teal), #0d9488); color: #fff; border: none; border-radius: var(--radius-sm); font-size: 1rem; font-weight: 700; cursor: pointer; transition: var(--transition); margin-top: 8px; letter-spacing: 0.5px; }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(20,184,166,0.4); }
        .error-msg { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #ef4444; padding: 12px 16px; border-radius: var(--radius-sm); font-size: 0.875rem; margin-bottom: 20px; }
        .remember-row { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
        .remember-row input[type="checkbox"] { accent-color: var(--teal); width: 16px; height: 16px; cursor: pointer; }
        .remember-row label { color: var(--text-secondary); font-size: 0.875rem; cursor: pointer; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <img src="{{ asset('images/logo_tagline.png') }}" alt="travine.id">
        </div>
        <h1 class="login-title">Admin Dashboard</h1>
        <p class="login-sub">Masuk untuk mengelola konten travine.id</p>

        @if($errors->any())
            <div class="error-msg">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <div class="form-group">
                <label class="form-label" for="username">Username / Email</label>
                <input id="username" name="username" type="text" class="form-control"
                       value="{{ old('username') }}" placeholder="admin" required autofocus>
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input id="password" name="password" type="password" class="form-control"
                       placeholder="••••••••" required>
            </div>
            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember">Ingat saya</label>
            </div>
            <button type="submit" class="btn-login">Masuk →</button>
        </form>
    </div>
</body>
</html>
