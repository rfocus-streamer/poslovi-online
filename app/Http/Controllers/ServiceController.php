<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceImage;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\CartItem;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get(); // Dohvati sve
        $topServices = Service::with([
            'user',
            'category',
            'subcategory',
            'serviceImages',
            'reviews',
            'cartItems'])->take(3)->get();

        // Dodajemo prosečnu ocenu za svaki servis u kolekciji
        $topServices->each(function ($service) {
            $service->average_rating = $service->reviews->count() > 0
                ? round($service->reviews->avg('rating'), 1)
                : 5;
        });

        $selectedCategoryIds = $topServices->pluck('id')->toArray(); // ID-jevi kategorija iz prvog upita

        $lastServices = Service::with([
            'user',
            'category',
            'subcategory',
            'serviceImages',
            'cartItems',
            'reviews'
        ])
        ->whereNotIn('id', $selectedCategoryIds) // Uklanjamo već odabrane kategorije
        ->orderBy('created_at', 'desc') // Poslednje dodate
        ->take(3)
        ->get();

        // Dodajemo prosečnu ocenu za svaki servis u kolekciji
        $lastServices->each(function ($service) {
            $service->average_rating = $service->reviews->count() > 0
                ? round($service->reviews->avg('rating'), 1)
                : 5;
        });


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
                    'topServices',
                    'lastServices',
                    'categories',
                    'favoriteCount',
                    'cartCount',
                    'projectCount',
                    'seller'
                ));
    }

    /**
    * Display a listing of the resource.
    */
    public function sellerServices()
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get(); // Dohvati sve
        $services = Service::with([
            'user',
            'category',
            'subcategory',
            'serviceImages',
            'cartItems'])->where('user_id', Auth::id())->get();

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
            $seller['countPublicService'] = Service::where('user_id', Auth::id())
                ->where('visible', 1)
                ->count();
            $projectCount = Project::where('buyer_id', Auth::id())->count();
        }

        return view('services.seller',
            compact(
                'services',
                'categories',
                'favoriteCount',
                'cartCount',
                'projectCount',
                'seller'
            )
        );
    }

    public function deleteServiceImage($imageId)
    {
       // Nađi sliku po ID-u
        $image = ServiceImage::find($imageId);

        if ($image) {
            // Obriši sliku sa diska
            $imagePath = storage_path('app/public/services/' . $image->image_path);
            if (file_exists($imagePath)) {
                unlink($imagePath); // Brisanje slike sa servera
            }

            // Obriši unos iz baze
            $image->delete();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 400); // Ako slika nije pronađena
    }

    /**
     * Prikaz forme za dodatnu naplatu.
     */
    public function create()
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get(); // Dohvati sve

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
            $seller['countPublicService'] = Service::where('user_id', Auth::id())
                ->where('visible', 1)
                ->count();
            $projectCount = Project::where('buyer_id', Auth::id())->count();
        }

        return view('services.create',
            compact(
                'categories',
                'favoriteCount',
                'cartCount',
                'projectCount',
                'seller'
            )
        );
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validacija podataka
        $validated = $request->validate([
            'category' => 'required|exists:categories,id',
            'subcategory' => 'required|numeric', // Promenjeno jer možda nemate subcategories tabelu
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_price' => 'required|numeric|min:0',
            'standard_price' => 'required|numeric|min:0',
            'premium_price' => 'required|numeric|min:0',
            'start_delivery_days' => 'required|integer|min:1',
            'standard_delivery_days' => 'required|integer|min:1',
            'premium_delivery_days' => 'required|integer|min:1',
            'start_inclusions' => 'required|string',
            'standard_inclusions' => 'required|string',
            'premium_inclusions' => 'required|string',
            'serviceImages.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $visible = $request->has('visible') ? 1 : 0;
        try {
            // Ažuriranje osnovnih podataka servisa
            $service = Service::create([
                'user_id' => Auth::id(),
                'category_id' => $validated['category'],
                'subcategory_id' => $validated['subcategory'], // Čuvamo ID bez provere relacije
                'title' => $validated['title'],
                'description' => $validated['description'],
                'basic_price' => $validated['start_price'],
                'standard_price' => $validated['standard_price'],
                'premium_price' => $validated['premium_price'],
                'basic_delivery_days' => $validated['start_delivery_days'],
                'standard_delivery_days' => $validated['standard_delivery_days'],
                'premium_delivery_days' => $validated['premium_delivery_days'],
                'basic_inclusions' => $validated['start_inclusions'],
                'standard_inclusions' => $validated['standard_inclusions'],
                'premium_inclusions' => $validated['premium_inclusions'],
                'visible' =>  ($visible === 0) ? null : $visible,
                'visible_expires_at' => ($visible === 0) ? null : now()->addMonth()
            ]);


            // Dodavanje novih slika
            if ($request->hasFile('serviceImages')) {
                $remainingSlots = 10 - $service->serviceImages()->count();

                if ($remainingSlots > 0) {
                    $images = $request->file('serviceImages');

                    // Ograničavamo broj slika na preostale slotove
                    $images = array_slice($images, 0, $remainingSlots);

                    foreach ($images as $image) {
                        $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                        // Čuvanje slike u storage
                        $image->storeAs('public/services', $filename);

                        // Čuvanje podataka u bazu
                        $service->serviceImages()->create([
                            'service_id' => $service->id,
                            'image_path' => $filename
                        ]);
                    }
                }
            }

            return redirect()->route('services.index', $service)
                ->with('success', 'Ponuda je uspešno dodata.');
        } catch (\Exception $e) {
            // U slučaju greške, vraćamo korisniku odgovarajući odgovor
            return back()->withErrors(['error' => 'Došlo je do greške prilikom kreiranja servisa. Pokušajte ponovo.'])
                         ->withInput();
        }
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
            'cartItems',
            'reviews'
        ])->findOrFail($id);

        // Izračunavanje prosečne ocene (ako nema ocena, podrazumevano 5)
        $averageRating = $service->reviews->count() > 0
            ? round($service->reviews->avg('rating'), 1)
            : 5;

        // Dodajemo prosečnu ocenu kao novu osobinu objekta
        $service->average_rating = $averageRating;

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

    public function viewServices(Service $service)
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get(); // Dohvati sve
        // Dohvati uslugu sa svim relacijama
        $service = Service::with([
            'user',
            'category',
            'subcategory',
            'serviceImages',
            'cartItems'
        ])->findOrFail($service->id);

        $reviews = $service->reviews()
                    ->with('user') // Eager load user za svaku recenziju
                    ->paginate(3);  // Paginacija direktno na relaciji

        // Izvuci user_id iz servisa
        $userId = $service->user_id;

        // Izračunaj broj servisa za tog korisnika
        $userServiceCount = Service::where('user_id', $userId)->count();

        $favoriteCount = 0;
        $cartCount = 0;
        $seller = [];

        if (Auth::check()) { // Proverite da li je korisnik ulogovan
            $favoriteCount = Favorite::where('user_id', Auth::id())->count();
            $cartCount = CartItem::where('user_id', Auth::id())->count();
            $seller['countPublicService'] = Service::where('user_id', Auth::id())
                ->where('visible', 1)
                ->count();
        }

        $title = $service->title;

        return view('services.edit', compact(
                    'title',
                    'service',
                    'categories',
                    'reviews',
                    'userServiceCount',
                    'favoriteCount',
                    'cartCount',
                    'seller'
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
    public function update(Service $service, Request $request)
    {
        // Validacija podataka
        $validated = $request->validate([
            'category' => 'required|exists:categories,id',
            'subcategory' => 'required|numeric', // Promenjeno jer možda nemate subcategories tabelu
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_price' => 'required|numeric|min:0',
            'standard_price' => 'required|numeric|min:0',
            'premium_price' => 'required|numeric|min:0',
            'start_delivery_days' => 'required|integer|min:1',
            'standard_delivery_days' => 'required|integer|min:1',
            'premium_delivery_days' => 'required|integer|min:1',
            'start_inclusions' => 'required|string',
            'standard_inclusions' => 'required|string',
            'premium_inclusions' => 'required|string',
            'serviceImages.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $visible = $request->has('visible') ? 1 : 0;
        // Ažuriranje osnovnih podataka servisa
        $service->update([
            'category_id' => $validated['category'],
            'subcategory_id' => $validated['subcategory'], // Čuvamo ID bez provere relacije
            'title' => $validated['title'],
            'description' => $validated['description'],
            'basic_price' => $validated['start_price'],
            'standard_price' => $validated['standard_price'],
            'premium_price' => $validated['premium_price'],
            'basic_delivery_days' => $validated['start_delivery_days'],
            'standard_delivery_days' => $validated['standard_delivery_days'],
            'premium_delivery_days' => $validated['premium_delivery_days'],
            'basic_inclusions' => $validated['start_inclusions'],
            'standard_inclusions' => $validated['standard_inclusions'],
            'premium_inclusions' => $validated['premium_inclusions'],
            'visible' => ($visible === 0 && $service->visible === null) ? null : $visible,
            'visible_expires_at' => ($visible === 0 && $service->visible === null) ? null : now()->addMonth()
        ]);


        // Dodavanje novih slika
        if ($request->hasFile('serviceImages')) {
            $remainingSlots = 10 - $service->serviceImages()->count();

            if ($remainingSlots > 0) {
                $images = $request->file('serviceImages');
                $images = array_slice($images, 0, $remainingSlots);
                $uploadSuccess = true;

                foreach ($images as $image) {
                    $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                    // Čuvanje slike u storage
                    $image->storeAs('public/services', $filename);

                    // Provera da li je slika zaista sačuvana
                    if (!Storage::exists('public/services/'.$filename)) {
                        $uploadSuccess = false;
                        continue; // Možete i break ako želite da prekinete dalje uploadovanje
                    }

                    // Čuvanje podataka u bazu
                    $service->serviceImages()->create([
                        'service_id' => $service->id,
                        'image_path' => $filename
                    ]);
                }

                if (!$uploadSuccess) {
                    return redirect()->route('services.index', $service)
                        ->with('error', 'Neke od slika nisu uspešno sačuvane. Proverite serverške permisije.');
                }
            } else {
                return redirect()->route('services.index', $service)
                    ->with('error', 'Dostigli ste maksimalan broj slika (10) za ovaj servis.');
            }
        }

        return redirect()->route('services.index', $service)
            ->with('success', 'Ponuda je uspešno ažurirana.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {

        if ($service->user_id !== Auth::id()) {
            abort(403); // Spreči brisanje tuđih stavki
        }

        // Prvo obrišemo slike vezane za servis
        foreach ($service->serviceImages as $image) {
            // Brisanje slike sa servera
            Storage::delete('public/services/' . $image->image_path);

            // Zatim obriši sliku iz baze
            $image->delete();
        }

        $service->delete();
        return redirect()
                ->back()
                ->with('success', "Uspešno ste uklonili ponudu");
    }
}
