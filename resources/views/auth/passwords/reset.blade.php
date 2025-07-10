<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password - Timetable Management System</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/login/images/favicon.ico') }}">

    <!-- Font Icon -->
    <link rel="stylesheet" href="{{ asset('assets/login/fonts/material-icon/css/material-design-iconic-font.min.css') }}">

    <!-- Main css -->
    <link rel="stylesheet" href="{{ asset('assets/login/css/style.css') }}">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            color: #333;
        }
        .signin-content {
            padding: 77px 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .signin-image {
            margin-right: 50px;
        }
        .signin-form {
            width: 100%;
            max-width: 400px;
            background: #fff;
            padding: 50px 45px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .form-title {
            color: #333;
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-group {
            position: relative;
            margin-bottom: 25px;
            overflow: hidden;
        }
        .form-group:last-child {
            margin-bottom: 0;
        }
        .form-group label {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            transition: all 0.3s ease;
        }
        .form-group input {
            width: 100%;
            display: block;
            border: none;
            border-bottom: 2px solid #e6e6e6;
            padding: 10px 0 10px 30px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            color: #333;
            outline: none;
            transition: all 0.3s ease;
        }
        .form-group input:focus {
            border-bottom-color: #6dabe4;
        }
        .form-group input:focus + label,
        .form-group input:valid + label {
            top: -10px;
            font-size: 12px;
            color: #6dabe4;
        }
        .form-submit {
            background: #6dabe4;
            color: #fff;
            border: none;
            padding: 15px 0;
            border-radius: 5px;
            display: block;
            font-size: 16px;
            font-weight: 600;
            margin: 30px 0 15px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }
        .form-submit:hover {
            background: #5d9cce;
            transform: translateY(-2px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .forgot-password {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            display: block;
            text-align: center;
            transition: all 0.3s ease;
        }
        .forgot-password:hover {
            color: #6dabe4;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
            font-size: 14px;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .alert ul {
            margin: 0;
            padding-left: 20px;
        }
        .alert li {
            margin-bottom: 5px;
        }
        .alert li:last-child {
            margin-bottom: 0;
        }
        @media (max-width: 768px) {
            .signin-content {
                padding: 30px 15px;
            }
            .signin-image {
                display: none;
            }
            .signin-form {
                padding: 30px 25px;
            }
        }
    </style>
</head>
<body>
    <div class="main">
        <div class="signin-content">
            <div class="signin-image">
                <figure><img src="{{ asset('assets/login/images/signin-image.jpg') }}" alt="Reset password"></figure>
            </div>

            <div class="signin-form">
                <h2 class="form-title">Reset Password</h2>
                
                @if (session('status'))
                    <div class="alert alert-success">
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

                <form method="POST" class="register-form" id="reset-form" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    
                    <div class="form-group">
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            placeholder="Your Email" 
                            value="{{ old('email', $request->email) }}" 
                            required 
                            autofocus
                        />
                        <label for="email"><i class="zmdi zmdi-email"></i></label>
                    </div>
                    
                    <div class="form-group">
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            placeholder="New Password" 
                            required 
                            autocomplete="new-password"
                        />
                        <label for="password"><i class="zmdi zmdi-lock"></i></label>
                    </div>
                    
                    <div class="form-group">
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password-confirm" 
                            placeholder="Confirm Password" 
                            required 
                            autocomplete="new-password"
                        />
                        <label for="password-confirm"><i class="zmdi zmdi-lock-outline"></i></label>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="form-submit">
                            {{ __('Reset Password') }}
                        </button>
                        
                        <a href="{{ route('login') }}" class="forgot-password">
                            <i class="zmdi zmdi-arrow-left"></i> {{ __('Back to Login') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="{{ asset('assets/login/vendor/jquery/jquery.min.js') }}"></script>
    <script>
        // Add animation to form inputs
        $(document).ready(function() {
            $('input').on('focus', function() {
                $(this).siblings('label').addClass('active');
            });
            
            $('input').on('blur', function() {
                if ($(this).val() === '') {
                    $(this).siblings('label').removeClass('active');
                }
            });
            
            // Check for pre-filled values on load
            $('input').each(function() {
                if ($(this).val() !== '') {
                    $(this).siblings('label').addClass('active');
                }
            });
        });
    </script>
</body>
</html>
