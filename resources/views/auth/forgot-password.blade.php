<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - OUK Timetabling System</title>
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

        .brand-body { position: relative; z-index: 1; }

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

        .step-list {
            position: relative;
            z-index: 1;
            list-style: none;
            margin: 0; padding: 0;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .step-list li {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            color: rgba(255,255,255,0.85);
            font-size: 14px;
            line-height: 1.5;
        }

        .step-num {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: rgba(255,127,80,0.3);
            border: 1.5px solid #ff7f50;
            color: #ff7f50;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }

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

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #6b7280;
            font-size: 13px;
            text-decoration: none;
            margin-bottom: 28px;
            transition: color 0.2s;
        }

        .back-link:hover { color: #037b90; }

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
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        .alert ul { margin: 6px 0 0; padding-left: 18px; }

        .form-group { margin-bottom: 24px; }

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

        .form-input::placeholder { color: #9ca3af; }

        .form-input:focus {
            border-color: #037b90;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(3, 123, 144, 0.12);
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
        }

        .btn-submit:hover { background: linear-gradient(135deg, #ff7f50, #e86c3a); box-shadow: 0 6px 20px rgba(255,127,80,0.35); }
        .btn-submit:active { transform: scale(0.97) !important; }

        /* ── Transitions ── */
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

        .back-link { transition: color 0.2s ease, transform 0.15s ease; }
        .back-link:hover { transform: translateX(-3px); }
        .form-input { transition: border-color 0.2s, background 0.2s, box-shadow 0.2s; }
        .btn-submit { transition: background 0.25s ease, transform 0.12s ease, box-shadow 0.2s ease !important; }
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
                    Reset your<br><span>password safely</span>
                </h1>
                <p class="brand-sub">
                    We'll send a secure link to your registered email so you can set a new password.
                </p>
            </div>

            <ul class="step-list">
                <li>
                    <span class="step-num">1</span>
                    Enter your registered email address below
                </li>
                <li>
                    <span class="step-num">2</span>
                    Check your inbox for the reset link
                </li>
                <li>
                    <span class="step-num">3</span>
                    Click the link and choose a new password
                </li>
            </ul>
        </div>

        <div class="form-panel">
            <div class="form-logo-mobile">
                <img src="{{ asset('ouk-logo.png') }}" alt="OUK Logo">
            </div>

            <a href="{{ route('login') }}" class="back-link">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Back to sign in
            </a>

            <div class="icon-wrap">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#037b90" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,13 2,6"/>
                </svg>
            </div>

            <h2 class="form-title">Forgot your password?</h2>
            <p class="form-subtitle">No problem — enter your email and we'll send you a reset link.</p>

            @if (session('status'))
                <div class="alert alert-success">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#065f46" stroke-width="2" style="flex-shrink:0;margin-top:1px"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

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

                <button type="submit" class="btn-submit">Send Reset Link</button>
            </form>
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
    </script>
</body>
</html>
