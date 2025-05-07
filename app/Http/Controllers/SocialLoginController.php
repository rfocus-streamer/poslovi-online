<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SocialLoginController extends Controller
{
    // Google Login
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $socialUser = Socialite::driver('google')->user();
        print_r($socialUser);
        die();
        try {
            $socialUser = Socialite::driver('google')->user();

            $user = User::updateOrCreate([
                'provider_id' => $socialUser->id,
                'provider' => 'google'
            ], [
                'name' => $socialUser->name,
                'email' => $socialUser->email,
                'provider_token' => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken,
            ]);

            Auth::login($user);
            return redirect()->intended('/');

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors('Došlo je do greške pri Google prijavi.');
        }
    }

    // Facebook Login
    public function redirectToFacebook()
    {
       return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $socialUser = Socialite::driver('facebook')->user();

            $user = User::updateOrCreate([
                'provider_id' => $socialUser->id,
                'provider' => 'facebook'
            ], [
                'name' => $socialUser->name,
                'email' => $socialUser->email,
                'provider_token' => $socialUser->token,
            ]);

            Auth::login($user);
            return redirect()->intended('/');

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors('Došlo je do greške pri Facebook prijavi.');
        }
    }
}
