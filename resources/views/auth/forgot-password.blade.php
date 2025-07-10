<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Timetable Management System</title>
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

        .forgot-container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            margin: auto;
            min-height: 90vh;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .form-section {
            flex: 1;
            padding: 40px 50px;
            background-color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .form-content-wrapper {
            max-width: 400px;
            margin: 0 auto;
            width: 100%;
        }

        .image-section {
            flex: 1;
            background-image: url('{{ asset('assets/login/images/login.jpg') }}'); /* Replace with your image path */
            background-size: cover;
            background-position: center;
            display: none; /* Hidden on small screens */
        }
        
        @media (min-width: 992px) {
            .image-section {
                display: block; /* Shown on large screens */
            }
        }

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
        .alert-success {
            background-color: #e8f5e9;
            color: #388e3c;
            border: 1px solid #a5d6a7;
        }
        .alert-danger {
            background-color: #ffebee;
            color: #d32f2f;
            border: 1px solid #ef9a9a;
        }
        .alert ul {
            margin: 0;
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
        
        .back-to-login {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
        }
        .back-to-login a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }
        .back-to-login a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <main class="forgot-container">
        <div class="form-section">
            <div class="form-content-wrapper">
                <h2 class="form-title">Forgot Password? 🔑</h2>
                <p class="form-description">No problem. Enter your email address below and we'll send you a link to reset your password.</p>

                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
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

                <form method="POST" action="{{ route('password.email') }}" id="forgot-password-form">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" class="form-input" placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
                    </div>

                    <button type="submit" class="form-button">Send Password Reset Link</button>
                </form>

                <div class="back-to-login">
                    <a href="{{ route('login') }}">← Back to Login</a>
                </div>
            </div>
        </div>
        <aside class="image-section">
            </aside>
    </main>
</body>
</html>