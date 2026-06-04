<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OUK Timetabling System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-container {
            display: flex;
            width: 100%;
            max-width: 1100px;
            min-height: 600px;
            margin: 24px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        /* ── Left: Branded Panel ── */
        .brand-panel {
            display: none;
            flex: 1;
            background: linear-gradient(145deg, #037b90 0%, #025f70 50%, #024d5c 100%);
            position: relative;
            overflow: hidden;
            padding: 48px;
            flex-direction: column;
            justify-content: space-between;
        }

        @media (min-width: 900px) {
            .brand-panel { display: flex; }
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            top: -80px;
            right: -80px;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }

        .brand-panel::after {
            content: '';
            position: absolute;
            bottom: -60px;
            left: -60px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            position: relative;
            z-index: 1;
        }

        .brand-logo img {
            height: 52px;
            width: auto;
            filter: brightness(0) invert(1);
        }

        .brand-body {
            position: relative;
            z-index: 1;
        }

        .brand-headline {
            font-size: 2rem;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.25;
            margin: 0 0 16px;
        }

        .brand-headline span {
            color: #ff7f50;
        }

        .brand-sub {
            font-size: 15px;
            color: rgba(255,255,255,0.7);
            line-height: 1.6;
            margin: 0;
        }

        .feature-list {
            position: relative;
            z-index: 1;
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,0.8);
            font-size: 14px;
        }

        .feature-list li .icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* ── Right: Form Panel ── */
        .form-panel {
            flex: 0 0 460px;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 48px 52px;
        }

        @media (max-width: 899px) {
            .form-panel { flex: 1; padding: 40px 28px; }
        }

        .form-logo-mobile {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 32px;
        }

        .form-logo-mobile img {
            height: 40px;
            width: auto;
        }

        @media (min-width: 900px) {
            .form-logo-mobile { display: none; }
        }

        .form-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #111827;
            margin: 0 0 6px;
        }

        .form-subtitle {
            font-size: 14px;
            color: #6b7280;
            margin: 0 0 32px;
        }

        .alert {
            padding: 12px 16px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-size: 13px;
        }

        .alert-success {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        .alert ul {
            margin: 6px 0 0;
            padding-left: 18px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
            letter-spacing: 0.01em;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            padding: 11px 14px 11px 42px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            color: #111827;
            background: #f9fafb;
            outline: none;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
        }

        .form-input::placeholder { color: #9ca3af; }

        .form-input:focus {
            border-color: #037b90;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(3, 123, 144, 0.12);
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            padding: 0;
            display: flex;
            align-items: center;
        }

        .password-toggle:hover { color: #6b7280; }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .remember-me label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #6b7280;
            cursor: pointer;
            user-select: none;
        }

        .remember-me input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: #037b90;
            cursor: pointer;
        }

        .forgot-link {
            font-size: 13px;
            color: #037b90;
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-link:hover { text-decoration: underline; }

        .btn-signin {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #037b90, #025f70);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.1s;
            letter-spacing: 0.02em;
        }

        .btn-signin:hover { background: linear-gradient(135deg, #ff7f50, #e86c3a); opacity: 1; }
        .btn-signin:active { transform: scale(0.99); }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
            color: #d1d5db;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .btn-google {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            background: #ffffff;
            color: #374151;
            font-size: 14px;
            font-weight: 500;
            font-family: inherit;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s;
        }

        .btn-google:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }

        /* ── Page transition animations ── */
        .login-container {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.4s ease, transform 0.4s ease;
        }

        .login-container.page-ready {
            opacity: 1;
            transform: translateY(0);
        }

        .login-container.page-exit {
            opacity: 0;
            transform: translateY(-16px);
            transition: opacity 0.28s ease, transform 0.28s ease;
        }

        .btn-signin {
            transition: background 0.25s ease, transform 0.12s ease, box-shadow 0.2s ease !important;
        }

        .btn-signin:hover {
            box-shadow: 0 6px 20px rgba(255,127,80,0.35);
        }

        .btn-signin:active { transform: scale(0.97) !important; }

        .btn-google { transition: background 0.2s ease, border-color 0.2s ease, transform 0.12s ease, box-shadow 0.2s ease; }
        .btn-google:active { transform: scale(0.97); }

        .back-link, .forgot-link { transition: color 0.2s ease; }

        .form-input { transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease; }
    </style>
</head>
<body>
    <main class="login-container">

        {{-- Left Branded Panel --}}
        <div class="brand-panel">
            <div class="brand-logo">
                <img src="{{ asset('ouk-logo.png') }}" alt="OUK Logo">
            </div>

            <div class="brand-body">
                <h1 class="brand-headline">
                    Smart Timetabling<br>for <span>Modern Learning</span>
                </h1>
                <p class="brand-sub">
                    Manage schedules, allocate resources, and coordinate academic activities — all from one unified platform.
                </p>
            </div>

            <ul class="feature-list">
                <li>
                    <span class="icon">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="rgba(255,255,255,0.9)" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </span>
                    Automated schedule generation
                </li>
                <li>
                    <span class="icon">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="rgba(255,255,255,0.9)" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </span>
                    Multi-role access control
                </li>
                <li>
                    <span class="icon">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="rgba(255,255,255,0.9)" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </span>
                    Real-time conflict detection
                </li>
            </ul>
        </div>

        {{-- Right Form Panel --}}
        <div class="form-panel">

            {{-- Logo shown only on mobile --}}
            <div class="form-logo-mobile">
                <img src="{{ asset('ouk-logo.png') }}" alt="OUK Logo">
            </div>

            <h2 class="form-title">Welcome back</h2>
            <p class="form-subtitle">Sign in to your OUK Timetabling account</p>

            <form method="POST" action="{{ route('login') }}" id="login-form">
                @csrf

                @if (session('status'))
                    <div class="alert alert-success" role="alert">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>{{ __('Whoops! Something went wrong.') }}</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label for="email">Email address</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </span>
                        <input type="email" name="email" id="email" class="form-input"
                               placeholder="you@ouk.ac.ke"
                               value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                        <input type="password" name="password" id="password" class="form-input"
                               placeholder="Enter your password" required autocomplete="current-password">
                        <button type="button" class="password-toggle" onclick="togglePassword()" aria-label="Toggle password visibility">
                            <svg id="eye-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <div class="remember-me">
                        <label for="remember">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>Remember me</span>
                        </label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                    @endif
                </div>

                <button type="submit" class="btn-signin">Sign In</button>
            </form>

            <div class="divider">or</div>

            <a href="{{ route('login.google') }}" class="btn-google">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Continue with Google
            </a>
        </div>
    </main>

    <script>
        /* ── Page transition animations ── */
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector('.login-container').classList.add('page-ready');
        });

        function navigateOut(url) {
            document.querySelector('.login-container').classList.add('page-exit');
            setTimeout(() => { window.location.href = url; }, 300);
        }

        document.querySelectorAll('a[href]').forEach(link => {
            const href = link.getAttribute('href');
            if (href && !href.startsWith('#') && !href.startsWith('http')) {
                link.addEventListener('click', e => {
                    e.preventDefault();
                    navigateOut(href);
                });
            }
        });

        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            }
        }
    </script>
</body>
</html>
