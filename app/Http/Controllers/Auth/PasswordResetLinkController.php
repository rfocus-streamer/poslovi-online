<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use App\Mail\ContactMail;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Ako je sve prošlo kako treba, pozivamo našu metodu za slanje personalizovanog emaila
        if ($status == Password::RESET_LINK_SENT) {
            // Prvo proverimo da li korisnik postoji u bazi
            $user = \App\Models\User::where('email', $request->email)->first();
            if ($user) {
                // Pozivamo našu metodu za slanje reset email-a
                $this->sendResetEmail($user);
            }

            return back()->with('status', __($status));
        } else {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
        }
    }

    public function sendResetEmail($user)
    {
        // Generišemo link za resetovanje lozinke (koristimo već ugrađenu logiku iz Laravel-a)
        $resetUrl = URL::temporarySignedRoute(
            'password.reset', // ime rute za reset lozinke
            now()->addMinutes(60), // link važi 1 sat
            [
                'email' => $user->email,
                'token' => app('auth.password.broker')->createToken($user),
            ]
        );

        // Definišemo podatke koje šaljemo u email
        $details = [
            'first_name' => $user->firstname,
            'last_name' => $user->lastname,
            'email' => $user->email,
            'message' => 'Kliknite na link ispod kako biste resetovali svoju lozinku.',
            'template' => 'emails.reset_password', // Predloženi Blade šablon
            'subject' => 'Resetujte vašu lozinku',
            'from_email' => config('mail.from.address'),
            'from' => 'Poslovi Online',
            'resetUrl' => $resetUrl, // URL za reset lozinke
        ];

        // Šaljemo email korisniku
        Mail::to($user->email)->send(new ContactMail($details));

        return back()->with('success', 'Email za resetovanje lozinke je uspešno poslat!');
    }
}
