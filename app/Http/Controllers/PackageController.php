<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\CartItem;
use App\Models\User;
use App\Models\Affiliate;
use App\Models\Subscription;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            // Dohvati trenutni mesec i godinu
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $totalEarnings = Commission::where('seller_id', Auth::id())
                                ->whereMonth('created_at', $currentMonth)
                                ->whereYear('created_at', $currentYear)
                                ->sum(DB::raw('amount - seller_amount'));

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

        if (!$user->package && $user->referred_by) {
            try {
                DB::transaction(function () use ($user, $price, $package) {
                    $affiliate = User::where('id', $user->referred_by)->lockForUpdate()->first();

                    if ($affiliate) {
                        $commission = $price * 0.7;

                        // Zaokruži na 2 decimale
                        $commission = round($commission, 2);

                        // Ažuriraj deposits
                        $affiliate->increment('affiliate_balance', $commission);

                        // Kreiraj istoriju transakcije
                        Affiliate::create([
                            'affiliate_id' => $affiliate->id,
                            'referral_id' => $user->id, // promenjeno
                            'package_id' => $package->id,
                            'amount' => $commission,
                            'percentage' => 70,
                            'status' => 'completed'
                        ]);
                    }
                });
            } catch (\Exception $e) {
                Log::error('Affiliate commission error: ' . $e->getMessage());
                // Možete dodati notifikaciju adminima o grešci
            }
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
