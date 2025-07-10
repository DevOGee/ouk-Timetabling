<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password - Timetable Management System</title>

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
        }
        .signin-content {
            padding: 77px 0;
        }
        .form-submit {
            background: #6dabe4;
            color: #fff;
            border: none;
            padding: 15px 39px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            display: inline-block;
            font-size: 16px;
            border-bottom: 3px solid #5d9cce;
            margin-bottom: 20px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }
        .form-submit:hover {
            background: #5d9cce;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
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
    </style>
</head>
<body>
    <div class="main">
        <section class="sign-in">
            <div class="container">
                <div class="signin-content">
                    <div class="signin-image">
                        <figure><img src="{{ asset('assets/login/images/signin-image.jpg') }}" alt="sign in image"></figure>
                    </div>

                    <div class="signin-form">
                        <h2 class="form-title">Reset Password</h2>
                        
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" class="register-form" id="reset-form" action="{{ route('password.email') }}">
                            @csrf
                            
                            <div class="form-group">
                                <label for="email"><i class="zmdi zmdi-email"></i></label>
                                <input type="email" name="email" id="email" placeholder="Your Email" value="{{ old('email') }}" required autofocus/>
                            </div>
                            
                            <div class="form-group form-button">
                                <button type="submit" class="form-submit">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                                
                                <a href="{{ route('login') }}" class="forgot-password" style="display: block; text-align: center; margin-top: 15px;">
                                    {{ __('Back to Login') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- JS -->
    <script src="{{ asset('assets/login/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/login/js/main.js') }}"></script>
</body>
</html>
