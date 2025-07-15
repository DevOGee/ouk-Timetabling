<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::updateOrCreate(
                ['email' => $googleUser->email],
                [
                    'name' => $googleUser->name,
                    'google_id' => $googleUser->id,
                    'password' => bcrypt(uniqid()),
                    'email_verified_at' => now(),
                ]
            );

            // Log the user in and remember them
            Auth::login($user, true);
            
            // Regenerate the session to prevent session fixation
            request()->session()->regenerate();
            
            // Redirect to the intended URL or dashboard
            return redirect()->intended('/dashboard');
            
        } catch (\Exception $e) {
            \Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect('/login')
                ->with('error', 'Failed to login with Google. Please try again or use another method.');
        }
    }
}
