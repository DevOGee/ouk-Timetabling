<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')
                ->scopes(['openid', 'profile', 'email'])
                ->redirect();
        } catch (\Exception $e) {
            Log::error('Google OAuth Redirect Error: ' . $e->getMessage());
            return redirect('/login')
                ->with('error', 'Unable to connect to Google. Please try again later.');
        }
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            if (!$googleUser->email) {
                Log::error('Google OAuth Error: No email provided by Google');
                return redirect('/login')
                    ->with('error', 'No email address provided by Google. Please try another login method.');
            }

            // Find or create user
            $user = User::updateOrCreate(
                ['email' => $googleUser->email],
                [
                    'name' => $googleUser->name,
                    'google_id' => $googleUser->id,
                    'password' => bcrypt(uniqid()),
                    'email_verified_at' => now(),
                ]
            );

            // Log the user in
            if (!Auth::loginUsingId($user->id, true)) {
                Log::error('Failed to log in user after Google OAuth', ['user_id' => $user->id]);
                return redirect('/login')
                    ->with('error', 'Login failed. Please try again.');
            }
            
            // Regenerate the session to prevent session fixation
            request()->session()->regenerate();
            
            Log::info('User logged in via Google OAuth', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            
            // Redirect to the intended URL or dashboard
            return redirect()->intended(route('dashboard'));
            
        } catch (InvalidStateException $e) {
            Log::error('Google OAuth Invalid State: ' . $e->getMessage());
            return redirect('/login')
                ->with('error', 'Session expired. Please try logging in again.');
                
        } catch (\Exception $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/login')
                ->with('error', 'Failed to login with Google. Please try again or use another method.');
        }
    }
}
