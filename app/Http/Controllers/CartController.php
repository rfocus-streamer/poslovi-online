<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Service;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Prikaz korpe
    public function index()
    {
        $cartItems = Auth::user()->cartItems()->with(['service', 'user', 'seller.privilegedCommission'])->get();
        return view('cart.index', compact('cartItems'));
    }

    // Dodaj servis u korpu
    public function store(Request $request, Service $service, string $package)
    {
        // Proveri da li je servis već u korpi
        $existingCartItem = CartItem::where('user_id', Auth::id())
            ->where('service_id', $service->id)
            ->where('package', $package) // Proveri paket
            ->first();

        if ($existingCartItem) {
            // Ako servis već postoji u korpi, povećaj količinu
            $existingCartItem->update([
                'quantity' => $existingCartItem->quantity + 1,
            ]);
        } else {
            // Ako servis ne postoji u korpi, dodaj ga
            CartItem::create([
                'user_id' => Auth::id(),
                'seller_id' => $service->user_id,
                'service_id' => $service->id,
                'package' => $package, // Dodaj paket
                'quantity' => 1,
            ]);
        }

        return redirect()
                ->back()
                ->with('success', "Uspešno si dodao u korpu $package paket, nastavi kupovinu unutar same korpe !");
                //->withFragment('cart-message'); // Skrolujte do elementa sa ID "cart-message"
    }

     // Ažuriraj količinu u korpi
    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'package' => 'required|in:Basic,Standard,Premium', // Validacija paketa
        ]);

        // Ažuriraj količinu i paket u bazi
        $cartItem->update([
            'quantity' => $request->quantity,
            'package' => $request->package,
        ]);

        return redirect()->back()->with('success', 'Količina i paket su ažurirani.');
    }


    // Ukloni servis iz korpe
    public function destroy(CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            abort(403); // Spreči brisanje tuđih stavki
        }

        $cartItem->delete();
        return redirect()
                ->back()
                ->with('error', "Uspešno si uklonio iz korpe uslugu")
                ->withFragment('cart-message-danger'); // Skrolujte do elementa sa ID "cart-message-danger"

    }
}
