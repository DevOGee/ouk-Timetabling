<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OUK Timetabling System</title>
    <link rel="stylesheet" href="{{ asset('assets/login/fonts/material-icon/css/material-design-iconic-font.min.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            margin: auto;
            min-height: 90vh;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .login-form-section {
            flex: 1;
            padding: 40px 50px;
            background-color: #fff;
            display: flex;
            flex-direction: column;
        }
        
        .form-content-wrapper {
            max-width: 400px;
            margin: auto;
            width: 100%;
        }

        .login-image-section {
            flex: 1;
            background-image: url('{{ asset('assets/login/images/login.jpg') }}'); /* Replace with your image path */
            background-size: cover;
            background-position: center;
            display: none; /* Hidden on small screens */
        }
        
        @media (min-width: 992px) {
            .login-image-section {
                display: block; /* Shown on large screens */
            }
        }

        /* --- Login Form Specific Styles --- */
        .form-title {
            font-size: 2em;
            font-weight: 600;
            margin-bottom: 10px;
            color: #222;
        }

        .form-description {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .alert {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
            text-align: left;
            font-size: 14px;
        }
        .alert-danger {
            background-color: #ffebee;
            color: #d32f2f;
            border: 1px solid #ef9a9a;
        }
        .alert ul {
            margin-top: 8px;
            padding-left: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-size: 14px;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
        }
        
        .remember-me label {
            display: flex;
            align-items: center;
            font-weight: 400;
            color: #555;
            cursor: pointer;
        }
        
        .remember-me input {
            margin-right: 8px;
        }
        
        .forgot-password a {
            color: #007bff;
            text-decoration: none;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }
        
        .form-button {
            width: 100%;
            padding: 12px 15px;
            background-color: #0d6efd;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-button:hover {
            background-color: #0b5ed7;
        }

        .divider {
            text-align: center;
            color: #aaa;
            margin: 25px 0;
            display: flex;
            align-items: center;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #eee;
        }
        .divider:not(:empty)::before {
            margin-right: .5em;
        }
        .divider:not(:empty)::after {
            margin-left: .5em;
        }
        
        .social-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            color: #333;
            text-decoration: none;
            transition: background-color 0.3s ease, color 0.3s ease;
            margin-bottom: 10px;
        }

        .social-button:hover {
            background-color: #f8f9fa;
        }
        
        .social-icon {
            font-size: 1.2em;
        }

        .auth-links {
            text-align: center;
            margin-top: auto;
            padding-top: 30px;
            font-size: 14px;
            color: #555;
        }
        .auth-links a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <main class="login-container">
        <div class="login-form-section">
            <div class="form-content-wrapper">
                <h2 class="form-title">Welcome to OUK Timetabling</h2>
                <p class="form-description">Sign in to access the timetabling system</p>

                <form method="POST" action="{{ route('login') }}" id="login-form">
                    @csrf

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <div>{{ __('Whoops! Something went wrong.') }}</div>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-input" placeholder="example@email.com" value="{{ old('email') }}" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-input" placeholder="At least 8 characters" required autocomplete="current-password">
                    </div>

                    <div class="form-group form-options">
                         <div class="remember-me">
                            <label for="remember">
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <span>Remember me</span>
                            </label>
                        </div>
                        @if (Route::has('password.request'))
                            <div class="forgot-password">
                                <a href="{{ route('password.request') }}">Forgot Password?</a>
                            </div>
                        @endif
                    </div>

                    <button type="submit" class="form-button">Sign In</button>
                </form>

                <div class="divider">Or continue with</div>

                <div class="social-login-buttons">
                    <a href="{{ route('login.google') }}" class="social-button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" style="margin-right: 8px;">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill-rule="evenodd"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill-rule="evenodd"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill-rule="evenodd"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill-rule="evenodd"/>
                            <path d="M1 1h22v22H1z" fill="none"/>
                        </svg>
                        Continue with Google
                    </a>
                </div>
            </div>
        </div>
        <aside class="login-image-section">
            </aside>
    </main>
</body>
</html>