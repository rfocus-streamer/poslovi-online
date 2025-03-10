<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Category;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'serviceImages'
        ])->findOrFail($id);

        $reviews = $service->reviews()
                    ->with('user') // Eager load user za svaku recenziju
                    ->paginate(3);  // Paginacija direktno na relaciji

        // Izvuci user_id iz servisa
        $userId = $service->user_id;

        // Izračunaj broj servisa za tog korisnika
        $userServiceCount = Service::where('user_id', $userId)->count();

        return view('services.show', compact('service', 'categories', 'reviews', 'userServiceCount'));
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
