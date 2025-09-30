<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Category;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get(); // Dohvati sve
        return view('auth.login', compact('categories'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if (! Auth::user()->is_verified) {
            $email = Auth::user()->email; // Uzimamo email trenutnog korisnika

             // Generišemo link za ponovno slanje verifikacije
            $resendLink = route('verification.resend', ['email' => $email]);

            Auth::logout();

            //return back()->with('error', 'Pre prijave potrebno je da verifikuješ email adresu putem linka koji ti je poslat.');
            return back()->with('error', 'Pre prijave potrebno je da verifikuješ email adresu putem linka koji ti je poslat. ' .
            'Ako nisi dobio/la email, možeš ga ponovo poslati <a href="' . $resendLink . '">ovde</a>.');
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
