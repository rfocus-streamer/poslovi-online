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
        try {
            $socialUser = Socialite::driver('google')->user();

            // Parsiranje imena i prezimena
            $fullName = explode(' ', $socialUser->name, 2);
            $firstname = $fullName[0];
            $lastname = $fullName[1] ?? '';

            // Provera da li korisnik već postoji na osnovu email adrese
            $user = User::where('email', $socialUser->email)->first();

            if (!$user) {
                // Novi korisnik - registracija
                $user = User::create([
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'email' => $socialUser->email,
                    'role' => 'buyer', // podrazumevana vrednost
                    'password' => Hash::make(Str::random(16)), // generišemo nasumičnu lozinku
                    'email_verified_at' => now(),
                    'is_verified' => true,
                    'avatar' => 'user.png',
                    'affiliate_code' => Str::random(10),
                ]);
            }

            Auth::login($user);
            return redirect()->intended('/');

        } catch (\Exception $e) {
            print_r($e);
            die();
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
