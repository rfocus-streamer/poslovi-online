<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\Category;
use Illuminate\Support\Str;

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
                'captcha' => ['required', 'numeric', 'in:'.session('math_captcha')],
            ],
            [
                'captcha.in' => 'Netačan odgovor na matematičko pitanje. Pokušajte ponovo.',
                'captcha.required' => 'Morate rešiti matematičku CAPTCHA-u.',
                'captcha.numeric' => 'Odgovor mora biti broj.',
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
            'role' => 'buyer', // Ubacujemo rolu
            'avatar' => 'user.jpg',
            'affiliate_code' => $this->generateUniqueAffiliateCode(),
            'referred_by' => $referredById,
        ]);

        event(new Registered($user));

        // Obriši CAPTCHA iz sesije nakon uspešne registracije
        session()->forget('math_captcha');

        //Auth::login($user);

        //return redirect(RouteServiceProvider::HOME);
        return redirect()->back()->with('success', 'Uspešno ste se registrovali. Proverite email i verifikujte nalog putem poslatog linka.');
    }

    protected function generateUniqueAffiliateCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (User::where('affiliate_code', $code)->exists());

        return $code;
    }
}
