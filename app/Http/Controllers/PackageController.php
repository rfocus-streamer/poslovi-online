<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\CartItem;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get(); // Dohvati sve
        $packages = Package::all(); // Dohvati sve
        $user = Auth::user();
        $favoriteCount = 0;
        $cartCount = 0;
        $totalEarnings = 0;

        if (Auth::check()) { // Proverite da li je korisnik ulogovan
            $favoriteCount = Favorite::where('user_id', Auth::id())->count();
            $cartCount = CartItem::where('user_id', Auth::id())->count();

        }

        if($user->role === 'buyer')
        {
            return redirect()->back()->with('error', 'Ukoliko želiš da budeš prodavac, ažuriraj profil sa označenim prodavcem (čekiraj) !');
        }

        return view('packages.index', compact('categories', 'packages', 'favoriteCount', 'cartCount', 'totalEarnings'));
    }

    public function activatePackage(Package $package)
    {
        $user = Auth::user();
        $price = 0;

        if($user->package){

            // Datum isteka aktivnog paketa
            $expiresAt = Carbon::parse($user->package_expires_at);
            $today = Carbon::now();

            // Broj preostalih dana
            $daysRemaining = $today->diffInDays($expiresAt, false); // false da dobijemo i negativne vrednosti ako je isteklo

            // Cena paketa
            $monthly_price = $user->package->price; // Cena paketa u dinarima/eurima (prilagodite)

            // Proporcionalni iznos preostale pretplate
            $daily_price = $monthly_price / 30; // Pretpostavljamo 30 dana u mesecu
            $remaining_amount = max(0, $daysRemaining * $daily_price); // Ako je isteklo, vraća 0
            $price = ($package->price - number_format($remaining_amount, 2, '.', ''));
        }else{
            $price = $package->price;
        }

        if($user->deposits < $price)
        {
            return redirect()->route('deposit.form')->with('error', 'Nema dovoljno sredstava na računu, deponuj !');
        }

        // Dodela paketa korisniku
        $user->package_id = $package->id; // Start paket
        $user->package_expires_at = now()->addMonth();
        $user->deposits -= $price;
        $user->save();

        // Sada kreiramo podatke o uplatama paketa
        $subscription = Subscription::create([
                'user_id' => Auth::id(),
                'package' => $package->id,
                'amount' => $price,
                'expires_at' => now()->addMonth()
        ]);

        // Dohvatanje svih korisnika određenog paketa
        //$users = Package::find(1)->users;

        return redirect()->back()->with('success', 'Uspešno ste aktivirali '.$package->name.'!');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Package $package)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Package $package)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Package $package)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Package $package)
    {
        //
    }
}
