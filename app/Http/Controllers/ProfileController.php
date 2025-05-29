<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use App\Models\Favorite;
use App\Models\CartItem;
use App\Models\Commission;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = Auth::user();
        $totalEarnings = 0;

        if (Auth::check()) { // Proverite da li je korisnik ulogovan
            // Dohvati trenutni mesec i godinu
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $totalEarnings = Commission::where('seller_id', Auth::id())
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum(DB::raw('CASE WHEN seller_amount > 0 THEN amount - seller_amount ELSE 0 END'));
        }

        return view('profile.edit', compact(
            'totalEarnings'
            )
        );
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validacija podataka
        $request->validate([
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB, samo slike
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            //'email' => 'required|email|unique:users,email,' . $user->id, // Proverava da email nije zauzet osim ako pripada korisniku
            'phone' => 'nullable|string',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
        ]);

        // Obrada avatara ako je postavljen novi
        if ($request->hasFile('avatar')) {
            // Brisanje starog avatara ako postoji
            if ($user->avatar && Storage::exists('public/user/' . $user->avatar)) {
                Storage::delete('public/user/' . $user->avatar);
            }

            // Čuvanje novog avatara
            $filename = time() .'_'.$user->id. '.' . $request->file('avatar')->getClientOriginalExtension();
            $request->file('avatar')->storeAs('public/user', $filename);

            // Ažuriranje avatara u bazi
            $user->avatar = $filename;
        }

        // Ažuriranje ostalih podataka
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->phone = $request->phone;
        $user->street = $request->street;
        $user->city = $request->city;
        $user->country = $request->country;

        // Čuvanje promena u bazi
        $user->save();

        return redirect()->back()->with('success', 'Profil uspešno ažuriran!');
    }

    public function UpdateRole(Request $request)
    {
        $user = Auth::user();

        // Validacija podataka
        $request->validate([
            'role' => 'string',
        ]);

        // Ažuriranje role
        if ($request->role != 'buyer' && $request->role != 'seller') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid role.'
            ]);
        }

        $user->role = $request->role;
        // Čuvanje promena u bazi
        $user->save();

        return response()->json([
            'success' => true,
            'role' => $user->role,  // Vraćaš novu rolu kao potvrdu
        ]);
    }

    public function changePassword(Request $request)
    {
        // Validacija inputa
        $request->validate([
            'new_password' => 'required|min:6|confirmed', // confirmed automatski proverava confirm_password
        ]);

        // Promeni lozinku i sačuvaj
        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Vrati poruku uspeha
        return redirect()->back()->with('success', 'Lozinka uspešno promenjena!');
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
