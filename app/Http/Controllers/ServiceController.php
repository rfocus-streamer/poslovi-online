<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\CartItem;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get(); // Dohvati sve
        $services = Service::with([
            'user',
            'category',
            'subcategory',
            'serviceImages',
            'cartItems'])->take(3)->get(); // Dohvati 4 usluga

        $favoriteCount = 0;
        $cartCount = 0;
        $seller = [];
        $projectCount = 0;

        if (Auth::check()) { // Proverite da li je korisnik ulogovan
            $favoriteCount = Favorite::where('user_id', Auth::id())->count();
            $cartCount = CartItem::where('user_id', Auth::id())->count();
            $seller['countProjects'] = Project::where('seller_id', Auth::id())
                ->whereNotIn('status', ['completed', 'uncompleted'])
                ->count();
            $projectCount = Project::where('buyer_id', Auth::id())->count();
        }

        return view('index', compact(
                    'services',
                    'categories',
                    'favoriteCount',
                    'cartCount',
                    'projectCount',
                    'seller'
                ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'images' => 'required|array|min:1|max:10',
        //     'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        // ]);

        // $service = Service::create([...]);

        // foreach ($request->file('images') as $image) {
        //     $path = $image->store('services', 'public');
        //     $service->serviceImages()->create(['image_path' => $path]);
        // }

        //return redirect()->route('services.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get(); // Dohvati sve
        // Dohvati uslugu sa svim relacijama
        $service = Service::with([
            'user',
            'category',
            'subcategory',
            'serviceImages',
            'cartItems'
        ])->findOrFail($id);

        $reviews = $service->reviews()
                    ->with('user') // Eager load user za svaku recenziju
                    ->paginate(3);  // Paginacija direktno na relaciji

        // Izvuci user_id iz servisa
        $userId = $service->user_id;

        // Izračunaj broj servisa za tog korisnika
        $userServiceCount = Service::where('user_id', $userId)->count();

        $favoriteCount = 0;
        $cartCount = 0;

        if (Auth::check()) { // Proverite da li je korisnik ulogovan
            $favoriteCount = Favorite::where('user_id', Auth::id())->count();
            $cartCount = CartItem::where('user_id', Auth::id())->count();
        }

        $title = $service->title;

        return view('services.show', compact(
                    'title',
                    'service',
                    'categories',
                    'reviews',
                    'userServiceCount',
                    'favoriteCount',
                    'cartCount'
                ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
