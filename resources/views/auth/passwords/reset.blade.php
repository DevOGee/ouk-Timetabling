<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - OUK Timetabling System</title>
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

        .page-container {
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

        @media (min-width: 900px) { .brand-panel { display: flex; } }

        .brand-panel::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 320px; height: 320px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }

        .brand-panel::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 240px; height: 240px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }

        .brand-logo img {
            height: 52px;
            width: auto;
            filter: brightness(0) invert(1);
            position: relative;
            z-index: 1;
        }

        .brand-body {
            position: relative;
            z-index: 1;
        }

        .brand-headline {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.25;
            margin: 0 0 16px;
        }

        .brand-headline span { color: #ff7f50; }

        .brand-sub {
            font-size: 15px;
            color: rgba(255,255,255,0.7);
            line-height: 1.6;
            margin: 0;
        }

        .tip-box {
            position: relative;
            z-index: 1;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 12px;
            padding: 20px;
        }

        .tip-box h4 {
            color: #ff7f50;
            font-size: 13px;
            font-weight: 600;
            margin: 0 0 12px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .tip-box ul {
            margin: 0; padding: 0;
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .tip-box li {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.8);
            font-size: 13px;
        }

        .tip-box li::before {
            content: '';
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #ff7f50;
            flex-shrink: 0;
        }

        /* ── Right: Form Panel ── */
        .form-panel {
            flex: 0 0 460px;
            background: #fff;
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
            margin-bottom: 32px;
        }

        .form-logo-mobile img { height: 40px; width: auto; }

        @media (min-width: 900px) { .form-logo-mobile { display: none; } }

        .icon-wrap {
            width: 52px; height: 52px;
            border-radius: 14px;
            background: rgba(3, 123, 144, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
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
            line-height: 1.6;
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

        .alert ul { margin: 6px 0 0; padding-left: 18px; }

        .form-group { margin-bottom: 20px; }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
            letter-spacing: 0.01em;
        }

        .input-wrapper { position: relative; }

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

        .form-input[readonly] {
            background: #f3f4f6;
            color: #6b7280;
            cursor: not-allowed;
        }

        .form-input::placeholder { color: #9ca3af; }

        .form-input:focus:not([readonly]) {
            border-color: #037b90;
            background: #fff;
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

        .strength-bar {
            margin-top: 8px;
            display: flex;
            gap: 4px;
        }

        .strength-bar span {
            flex: 1;
            height: 3px;
            border-radius: 2px;
            background: #e5e7eb;
            transition: background 0.3s;
        }

        .strength-label {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 4px;
        }

        .btn-submit {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #037b90, #025f70);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            letter-spacing: 0.02em;
            margin-top: 8px;
        }

        .btn-submit:hover { background: linear-gradient(135deg, #ff7f50, #e86c3a); box-shadow: 0 6px 20px rgba(255,127,80,0.35); }
        .btn-submit:active { transform: scale(0.97) !important; }

        .page-container {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.4s ease, transform 0.4s ease;
        }

        .page-container.page-ready { opacity: 1; transform: translateY(0); }
        .page-container.page-exit {
            opacity: 0;
            transform: translateY(-16px);
            transition: opacity 0.28s ease, transform 0.28s ease;
        }

        .form-input { transition: border-color 0.2s, background 0.2s, box-shadow 0.2s; }
        .btn-submit { transition: background 0.25s ease, transform 0.12s ease, box-shadow 0.2s ease !important; }
        .password-toggle { transition: color 0.2s; }

        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: #6b7280;
        }

        .login-link a {
            color: #037b90;
            font-weight: 500;
            text-decoration: none;
        }

        .login-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <main class="page-container">

        <div class="brand-panel">
            <div class="brand-logo">
                <img src="{{ asset('ouk-logo.png') }}" alt="OUK Logo">
            </div>

            <div class="brand-body">
                <h1 class="brand-headline">
                    Choose a<br><span>strong password</span>
                </h1>
                <p class="brand-sub">
                    Your new password will be used to secure your OUK Timetabling account.
                </p>
            </div>

            <div class="tip-box">
                <h4>Password tips</h4>
                <ul>
                    <li>At least 8 characters long</li>
                    <li>Mix uppercase and lowercase letters</li>
                    <li>Include numbers and special characters</li>
                    <li>Avoid using your name or email</li>
                </ul>
            </div>
        </div>

        <div class="form-panel">
            <div class="form-logo-mobile">
                <img src="{{ asset('ouk-logo.png') }}" alt="OUK Logo">
            </div>

            <div class="icon-wrap">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#037b90" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>

            <h2 class="form-title">Set new password</h2>
            <p class="form-subtitle">Enter your new password below. Make sure it's something you'll remember.</p>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Please fix the following:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label for="email">Email address</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </span>
                        <input type="email" name="email" id="email" class="form-input"
                               value="{{ old('email', $email ?? request('email')) }}"
                               required readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">New password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                        <input type="password" name="password" id="password" class="form-input"
                               placeholder="New password" required autocomplete="new-password"
                               oninput="checkStrength(this.value)">
                        <button type="button" class="password-toggle" onclick="togglePwd('password','eye1')" aria-label="Toggle password">
                            <svg id="eye1" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <div class="strength-bar">
                        <span id="s1"></span>
                        <span id="s2"></span>
                        <span id="s3"></span>
                        <span id="s4"></span>
                    </div>
                    <div class="strength-label" id="strength-label"></div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-input"
                               placeholder="Confirm password" required autocomplete="new-password">
                        <button type="button" class="password-toggle" onclick="togglePwd('password_confirmation','eye2')" aria-label="Toggle confirm password">
                            <svg id="eye2" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Reset Password</button>
            </form>

            <p class="login-link">
                Remembered it? <a href="{{ route('login') }}">Back to sign in</a>
            </p>
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector('.page-container').classList.add('page-ready');
        });

        function navigateOut(url) {
            document.querySelector('.page-container').classList.add('page-exit');
            setTimeout(() => { window.location.href = url; }, 280);
        }

        document.querySelectorAll('a[href]').forEach(link => {
            const href = link.getAttribute('href');
            if (href && !href.startsWith('#') && !href.startsWith('http')) {
                link.addEventListener('click', e => { e.preventDefault(); navigateOut(href); });
            }
        });

        function togglePwd(id, iconId) {
            const input = document.getElementById(id);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            }
        }

        function checkStrength(val) {
            const bars = [document.getElementById('s1'), document.getElementById('s2'),
                          document.getElementById('s3'), document.getElementById('s4')];
            const label = document.getElementById('strength-label');
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
            if (/\d/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const colors = ['#ef4444','#f97316','#eab308','#22c55e'];
            const labels = ['Weak','Fair','Good','Strong'];
            bars.forEach((b, i) => {
                b.style.background = i < score ? colors[score - 1] : '#e5e7eb';
            });
            label.textContent = val.length ? labels[score - 1] || '' : '';
            label.style.color = score ? colors[score - 1] : '#9ca3af';
        }
    </script>
</body>
</html>
