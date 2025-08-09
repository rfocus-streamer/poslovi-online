<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Mail\ContactMail;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get(); // Dohvati sve
        return view('auth.register', compact('categories'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
                'firstname' => ['required', 'string', 'max:255'],
                'lastname' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'street' => ['required', 'string', 'max:255'],
                'city' => ['required', 'string', 'max:255'],
                'country' => ['required', 'string', 'max:255'],
                'captcha' => ['required', 'numeric', 'in:'.session('math_captcha')],
            ],
            [
                'captcha.in' => 'Netačan odgovor na matematičko pitanje. Pokušaj ponovo.',
                'captcha.required' => 'Moraš rešiti matematičku CAPTCHA-u.',
                'captcha.numeric' => 'Odgovor mora biti broj.',
                'terms.accepted' => 'Moraš prihvatiti uslove korišćenja.',
            ]
        );

       // Proveri i obradi affiliate kod PRE kreiranja korisnika
        $referredById = null;
        if ($request->affiliateCode) {
            $referrer = User::where('affiliate_code', $request->affiliateCode)->first();

            if ($referrer && $referrer->id !== auth()->id()) {
                $referredById = $referrer->id;
            } else {
                Log::warning("Invalid or self-referral affiliate code: " . $request->affiliateCode);
            }
        }

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'street' => $request->street,
            'city' => $request->city,
            'country' => $request->country,
            'role' => 'buyer', // Ubacujemo rolu
            'avatar' => 'user.jpg',
            'affiliate_code' => $this->generateUniqueAffiliateCode(),
            'referred_by' => $referredById,
        ]);

        event(new Registered($user));

        // Obriši CAPTCHA iz sesije nakon uspešne registracije
        session()->forget('math_captcha');

        $this->sendEmail($user);

        //Auth::login($user);

        //return redirect(RouteServiceProvider::HOME);
        return redirect()->back()->with('success', 'Uspešno si se registrovao. Proveri svoj email i verifikuj nalog putem poslatog linka.');
    }

    protected function generateUniqueAffiliateCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (User::where('affiliate_code', $code)->exists());

        return $code;
    }

    public function verify($id, $hash, Request $request)
    {
        $user = User::findOrFail($id);

        // Proveri da li hash odgovara korisnikovom emailu
        if (! hash_equals(sha1($user->email), $hash)) {
            abort(403, 'Nevažeći verifikacioni link.');
        }

        // Proveri da li je već verifikovan
        if ($user->is_verified) {
            return redirect('/login')->with('success', 'Email je već verifikovan. Možeš se prijaviti.');
        }

        // Obeleži kao verifikovan
        $user->is_verified = true;
        $user->email_verified_at = now();
        $user->save();

        return redirect('/login')->with('success', 'Uspešno si verifikovao svoj email.Možeš se sada prijaviti.');
    }

    private function sendEmail($user)
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.email', // ime rute
            now()->addDays(7),    // link važi 7 dana
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        // Pravimo asocijativni niz sa samo potrebnim podacima
        $details = [
            'first_name' => $user->firstname,
            'last_name' => $user->lastname,
            'email' => $user->email,
            'message' => 'Telo poruke iz kontrolera', // Možete dodati dinamicki tekst
            'template' => 'emails.activate_register', // Predloženi Blade šablon,
            'subject' => 'Potvrda email adrese',
            'from_email' => config('mail.from.address'),
            'from' => 'Poslovi Online',
            'verificationUrl' => $verificationUrl,
        ];

        Mail::to($user->email)->send(new ContactMail($details));

        return back()->with('success', 'Email je uspešno poslat!');
    }
}
