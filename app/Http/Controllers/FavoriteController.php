<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Service;
use App\Models\Category;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {


        $favoriteServices = []; // Inicijalizuj prazan niz za omiljene servise

        if (Auth::check()) { // Proverite da li je korisnik ulogovan
            // Dohvati omiljene servise za trenutno ulogovanog korisnika sa paginacijom
            $favoriteServices = Favorite::with('service', 'category') // Eager load relaciju sa Service
                ->where('user_id', Auth::id())
                ->paginate(50); // Paginacija sa 50 stavke po stranici
        }

        return view('favorites.index', compact('favoriteServices'));
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('query');

        // Pretraga omiljenih servisa
        $favoriteServices = Favorite::with('service', 'category')
            ->whereHas('service', function ($query) use ($searchTerm) {
                $query->where('title', 'like', '%' . $searchTerm . '%')
                      ->orWhere('description', 'like', '%' . $searchTerm . '%');
            })
            ->where('user_id', Auth::id());

        return view('favorites.partials.table', compact('favoriteServices'));
    }

    // Dodaj servis u omiljene
    public function store(Request $request, Service $service)
    {
        // Proveri da li je servis već u omiljenim
        $existingFavorite = Favorite::where('user_id', Auth::id())
            ->where('service_id', $service->id)
            ->first();

        if ($existingFavorite) {
            return redirect()->back()->with('error', 'Servis je već u omiljenim.');
        }

        // Dodaj u omiljene
        Favorite::create([
            'user_id' => Auth::id(),
            'service_id' => $service->id,
        ]);

        return redirect()->back()->with('success', 'Servis je dodat u omiljene.');
    }

    // Ukloni servis iz omiljenih
    public function destroy(Favorite $favorite)
    {
        if ($favorite->user_id !== Auth::id()) {
            abort(403); // Spreči brisanje tuđih stavki
        }

        $favorite->delete();

        if ($favorite) {
            $favorite->delete();
            return redirect()->back()->with('success', 'Servis je uklonjen iz omiljenih.');
        }

        return redirect()->back()->with('error', 'Servis nije pronađen u omiljenim.');
    }
}
