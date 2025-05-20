@extends('layouts.app')

@section('content')
<style>
    .bg-animated {
        background: linear-gradient(-45deg, #4f46e5, #9333ea, #0ea5e9, #1e293b);
        background-size: 400% 400%;
        animation: gradientBG 15s ease infinite;
    }
    @keyframes gradientBG {
        0% {background-position: 0% 50%}
        50% {background-position: 100% 50%}
        100% {background-position: 0% 50%}
    }
    .glass-card {
        background: rgba(30, 41, 59, 0.85);
        backdrop-filter: blur(14px) saturate(150%);
        border-radius: 2rem;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        border: 1px solid rgba(255,255,255,0.08);
    }
    .fancy-input {
        background: rgba(17, 24, 39, 0.95);
        color: #fff !important;
        border: 1.5px solid #6366f1;
        border-radius: 0.75rem;
        padding: 0.85rem 1.2rem;
        font-size: 1rem;
        transition: box-shadow 0.2s, border-color 0.2s;
    }
    .fancy-input:focus {
        outline: none;
        border-color: #a21caf;
        box-shadow: 0 0 0 3px #a21caf44;
        background: #1e293b;
    }
    .fancy-input::placeholder {
        color: #c7d2fe;
        opacity: 1;
    }
    .fancy-btn {
        background: linear-gradient(90deg, #6366f1 0%, #a21caf 100%);
        color: #fff;
        border: none;
        border-radius: 0.75rem;
        font-weight: 600;
        padding: 0.85rem 1.2rem;
        transition: background 0.2s, transform 0.1s;
        box-shadow: 0 4px 16px 0 #6366f144;
    }
    .fancy-btn:hover {
        background: linear-gradient(90deg, #a21caf 0%, #6366f1 100%);
        transform: translateY(-2px) scale(1.02);
    }
</style>
<div class="min-h-screen flex items-center justify-center bg-animated p-4">
    <div class="relative w-full max-w-md">
        <!-- Glassmorphism Card -->
        <div class="relative z-10 bg-white/10 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 overflow-hidden animate-fade-in-up">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-indigo-500 opacity-30 rounded-full filter blur-2xl animate-pulse"></div>
            <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-purple-500 opacity-30 rounded-full filter blur-2xl animate-pulse animation-delay-2000"></div>
            <div class="px-10 pt-10 pb-8">
                <div class="text-center mb-8">
                    <div class="mx-auto h-16 w-16 flex items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl mb-4 shadow-lg">
                        <svg class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-extrabold text-white drop-shadow mb-2">Sign in to your account</h2>
                    <p class="text-sm text-gray-200">
                        Or
                        <a href="{{ route('register') }}" class="font-medium text-indigo-300 hover:text-indigo-100 transition">
                            create a new account
                        </a>
                    </p>
                </div>
                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-300 bg-green-900/40 px-4 py-2 rounded">
                        {{ session('status') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4">
                        <div class="font-medium text-red-300">{{ __('Whoops! Something went wrong.') }}</div>
                        <ul class="mt-3 list-disc list-inside text-sm text-red-300">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form class="space-y-6 mt-8" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-200 mb-1">Email address</label>
                            <input id="email" name="email" type="email" autocomplete="email" required
    class="fancy-input w-full" value="{{ old('email') }}" placeholder="Enter your email address" autofocus>
                            @error('email')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-200 mb-1">Password</label>
                            <input id="password" name="password" type="password" autocomplete="current-password" required
    class="fancy-input w-full" placeholder="Enter your password">
                            @error('password')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember_me" name="remember" type="checkbox"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-600 rounded bg-gray-700">
                                <label for="remember_me" class="ml-2 block text-sm text-gray-300">Remember me</label>
                            </div>
                            @if (Route::has('password.request'))
                                <div class="text-sm">
                                    <a href="{{ route('password.request') }}" class="font-medium text-indigo-300 hover:text-indigo-100 transition">Forgot your password?</a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="fancy-btn w-full flex justify-center items-center py-3 px-4">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-indigo-300 group-hover:text-indigo-100 transition" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            Sign in
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

