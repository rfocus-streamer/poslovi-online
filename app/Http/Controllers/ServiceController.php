<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ForcedService;
use App\Models\ServiceImage;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\CartItem;
use App\Models\Project;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $forcedIds = ForcedService::orderBy('priority')
            ->pluck('service_id')
            ->take(3)
            ->toArray();

        // Prvo dohvatamo forsirane servise
        $forcedTopServices = Service::with([
                'user',
                'category',
                'subcategory',
                'serviceImages',
                'reviews',
                'cartItems'
            ])
            ->whereIn('id', $forcedIds)
            ->get();

        // Zatim dohvatamo ostale servise koji ispunjavaju uslove i nisu već forsirani
        $otherTopServices = Service::where('visible', true)
            ->whereNotNull('visible_expires_at')
            ->where('visible_expires_at', '>=', now())
            ->whereNotIn('id', $forcedIds) // Da ne dupliramo
            ->with([
                'user',
                'category',
                'subcategory',
                'serviceImages',
                'reviews',
                'cartItems'
            ])
            ->take(3 - $forcedTopServices->count()) // Uzimamo samo koliko nam fali do 3
            ->get();

        // Spajamo kolekcije u jedan set top servisa
        $topServices = $forcedTopServices->merge($otherTopServices);

        // Dodajemo prosečnu ocenu za svaki servis u kolekciji
        $topServices->each(function ($service) {
            $service->average_rating = $service->reviews->count() > 0
                ? round($service->reviews->avg('rating'), 1)
                : 5;
        });


        $selectedCategoryIds = $topServices->pluck('id')->toArray(); // ID-jevi kategorija iz prvog upita

        // Provera za poslednje dodate servise koji imaju postavljen datum isteka (nije null)
        $lastServices = Service::where('visible', true)
            ->whereNotNull('visible_expires_at')  // Proverava da li je datum isteka postavljen
            ->where('visible_expires_at', '>=', now())  // Proverava da datum isteka nije prošao
            ->with([
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

        if ($request->has('search')) {
            $searchTerm = $request->input('search');

            $services = Service::where('visible', true)
                ->whereNotNull('visible_expires_at')  // Proverava da li je datum isteka postavljen
                ->where('visible_expires_at', '>=', now())  // Proverava da datum isteka nije prošao
                ->with([
                    'user',
                    'category',
                    'subcategory',
                    'serviceImages',
                    'reviews',
                    'cartItems'
                ])
                ->where(function($query) use ($searchTerm) {
                    $query->where('title', 'like', "%{$searchTerm}%")
                          ->orWhere('description', 'like', "%{$searchTerm}%")
                          ->orWhereHas('category', function($q) use ($searchTerm) {
                              $q->where('name', 'like', "%{$searchTerm}%");
                          })
                          ->orWhereHas('subcategory', function($q) use ($searchTerm) {
                              $q->where('name', 'like', "%{$searchTerm}%");
                          });
                })
                ->orderBy('created_at', 'desc')
                ->paginate(6);

            $services->each(function ($service) {
                $service->average_rating = $service->reviews->count() > 0
                    ? round($service->reviews->avg('rating'), 1)
                    : 5;
            });

            $searchCategory = '';

            if ($request->has('category')) {
                $searchCategory = $request->input('category');
            }

            return view('index', [
                'services' => $services,
                'searchTerm' => $searchTerm,
                'topServices' => $topServices,
                'lastServices' => $lastServices,
                'searchCategory' => $searchCategory
            ]);
        }

        return view('index', compact(
            'topServices',
            'lastServices'
        ));
    }

    /**
    * Display a listing of the resource.
    */
    public function sellerServices()
    {
        if(Auth::user()->role !== 'seller')
        {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        $services = Service::with([
            'user',
            'category',
            'subcategory',
            'serviceImages',
            'cartItems'])->where('user_id', Auth::id())->get();

        $seller = [];

        if (Auth::check()) { // Proverite da li je korisnik ulogovan
            $seller['countProjects'] = Project::where('seller_id', Auth::id())
                ->whereNotIn('status', ['completed', 'uncompleted'])
                ->count();
            $seller['countPublicService'] = Service::where('user_id', Auth::id())
                ->where('visible', 1)
                ->count();
        }

        return view('services.seller',
            compact(
                'services',
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
        $seller = [];

        if (Auth::check()) { // Proverite da li je korisnik ulogovan
            $seller['countProjects'] = Project::where('seller_id', Auth::id())
                ->whereNotIn('status', ['completed', 'uncompleted'])
                ->count();
            $seller['countPublicService'] = Service::where('user_id', Auth::id())
                ->where('visible', 1)
                ->count();
        }

        return view('services.create',
            compact(
                'categories',
                'seller'
            )
        );
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Povećanje limita za upload
        ini_set('upload_max_filesize', '20M');
        ini_set('post_max_size', '20M');
        ini_set('max_execution_time', '300');
        ini_set('max_input_time', '300');

        // Osnovna validacija koja je uvek potrebna
        $request->validate([
            'category' => ['required', 'exists:categories,id'],
            'subcategory' => ['required', 'numeric'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'serviceImages.*' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ], [
            'category.required' => 'Morate izabrati kategoriju.',
            'category.exists' => 'Izabrana kategorija nije validna.',
            'subcategory.required' => 'Morate izabrati potkategoriju.',
            'subcategory.numeric' => 'Potkategorija mora biti numerička vrednost.',
            'title.required' => 'Naslov je obavezan.',
            'title.max' => 'Naslov ne sme biti duži od :max karaktera.',
            'description.required' => 'Opis je obavezan.',
            'serviceImages.*.image' => 'Svaka datoteka mora biti slika.',
            'serviceImages.*.mimes' => 'Dozvoljeni formati slika su: jpeg, png, jpg, gif.',
            'serviceImages.*.max' => 'Svaka slika može imati maksimalno 2048 KB.', // ili MB ako koristiš 2048
        ]);

        // Dinamička validacija za pakete
        $packageRules = [];
        $packageFields = ['price', 'delivery_days', 'inclusions'];
        $availablePackages = ['basic', 'standard', 'premium'];

        // Proveravamo koje pakete je korisnik poslao
        $submittedPackages = [];
        foreach ($availablePackages as $package) {
            if ($request->has($package.'_price')) {
                $submittedPackages[] = $package;

                // Dodajemo pravila validacije za svaki prisutan paket
                $packageRules[$package.'_price'] = 'required|numeric|min:0';
                $packageRules[$package.'_delivery_days'] = 'required|integer|min:1';
                $packageRules[$package.'_inclusions'] = 'required|string';
            }
        }

        // Proveravamo da li je poslat barem jedan paket
        if (empty($submittedPackages)) {
            return back()->withErrors(['error' => 'Moraš dodati barem jedan paket.'])->withInput();
        }

        // Spajamo osnovnu i dinamičku validaciju
        $validated = $request->validate(array_merge([
            'category' => 'required|exists:categories,id',
            'subcategory' => 'required|numeric',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'serviceImages.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], $packageRules));

        $visible = 0;

        if(Auth::user()->package){
            $countPublicService = Service::where('user_id', Auth::id())->where('visible', 1)->count();
            if($countPublicService < Auth::user()->package->quantity){
                $visible = $request->has('visible') ? 1 : 0;
            }
        }

        try {
            // Priprema podataka za čuvanje
            $serviceData = [
                'user_id' => Auth::id(),
                'category_id' => $validated['category'],
                'subcategory_id' => $validated['subcategory'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'visible' => ($visible === 0) ? null : $visible,
                'visible_expires_at' => ($visible === 0) ? null : now()->addMonth()
            ];

            // Dodajemo podatke za svaki paket koji je poslat
            foreach ($submittedPackages as $package) {
                $serviceData[$package.'_price'] = $validated[$package.'_price'];
                $serviceData[$package.'_delivery_days'] = $validated[$package.'_delivery_days'];
                $serviceData[$package.'_inclusions'] = $validated[$package.'_inclusions'];

                // Za pakete koji nisu poslati, postavljamo null vrednosti
                if (!in_array($package, $submittedPackages)) {
                    $serviceData[$package.'_price'] = null;
                    $serviceData[$package.'_delivery_days'] = null;
                    $serviceData[$package.'_inclusions'] = null;
                }
            }

            // Kreiranje servisa
            $service = Service::create($serviceData);

            // Dodavanje slika (ostaje isto kao u originalnom kodu)
            if ($request->hasFile('serviceImages')) {
                $remainingSlots = 10 - $service->serviceImages()->count();

                if ($request->ajax() and $remainingSlots === 0) {
                    return response()->json([
                        'error' => 'Dostigli ste maksimalan broj slika (10) za ovaj servis !'
                    ]);
                }

                $maxSize = ini_get('upload_max_filesize');

                if ($remainingSlots > 0) {
                    $images = $request->file('serviceImages');
                    $images = array_slice($images, 0, $remainingSlots);
                    $uploadSuccess = true;
                    $errorMessage = '';

                    $directory = 'public/services';
                    if (!Storage::exists($directory)) {
                        Storage::makeDirectory($directory, 0755, true);
                    }

                    foreach ($images as $image) {
                        if ($image->getSize() > 2 * 1024 * 1024) {
                            continue;
                        }

                        $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                        try {
                            $image->storeAs($directory, $filename);

                            if (!Storage::exists($directory.'/'.$filename)) {
                                throw new \Exception("Slika nije sačuvana na disku");
                            }

                            $service->serviceImages()->create([
                                'service_id' => $service->id,
                                'image_path' => $filename
                            ]);

                        } catch (\Exception $e) {
                            $uploadSuccess = false;
                            $errorMessage = 'Došlo je do greške pri čuvanju slika: ' . $e->getMessage();
                            \Log::error('Image upload failed: ' . $e->getMessage());
                            break;
                        }
                    }

                    if (!$uploadSuccess) {
                        return redirect()->route('services.index', $service)
                            ->with('error', $errorMessage);
                    }
                } else {
                    return redirect()->route('services.index', $service)
                        ->with('error', 'Dostigli ste maksimalan broj slika (10) za ovaj servis.');
                }
            }

            if ($request->ajax()) {
                $successMessage = 'Ponuda '.$validated['title'].' je uspešno dodata.';
                $request->session()->flash('success', $successMessage);

                return response()->json([
                    'redirect' => route('services.index', $service)
                ]);
            }

            return redirect()->route('services.index', $service)
                ->with('success', 'Ponuda je uspešno dodata.');

        } catch (\Exception $e) {
            \Log::error('Service creation error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Došlo je do greške prilikom kreiranja servisa. Pokušajte ponovo.'])
                         ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Dohvati uslugu sa svim relacijama
        $service = Service::with([
            'user',
            'category',
            'subcategory',
            'serviceImages',
            'cartItems',
            'reviews'
        ])->findOrFail($id);

        // Izračunavanje prosečne ocene (ako nema ocena, podrazumevano 0)
        $averageRating = $service->reviews->count() > 0
            ? round($service->reviews->avg('rating'), 1)
            : 0;

        // Dodajemo prosečnu ocenu kao novu osobinu objekta
        $service->average_rating = $averageRating;

        $reviews = $service->reviews()
                    ->with('user') // Eager load user za svaku recenziju
                    ->paginate(3);  // Paginacija direktno na relaciji

        // Izvuci user_id iz servisa
        $userId = $service->user_id;

        // Izračunaj broj servisa za tog korisnika
        $userServiceCount = Service::where('user_id', $userId)->count();

        $title = $service->title;

        return view('services.show', compact(
                    'title',
                    'service',
                    'reviews',
                    'userServiceCount',
                ));
    }

    public function viewServices(Service $service)
    {
        if(Auth::user()->role !== 'seller')
        {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        // Kategorije
        $categories = Category::with('subcategories')->whereNull('parent_id')->get();

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

        $seller = [];

        if (Auth::check()) { // Proverite da li je korisnik ulogovan
            $seller['countPublicService'] = Service::where('user_id', Auth::id())
                ->where('visible', 1)
                ->count();
        }

        $title = $service->title;

        return view('services.edit', compact(
                    'categories',
                    'title',
                    'service',
                    'reviews',
                    'userServiceCount',
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
        // Povećanje limita za upload
        ini_set('upload_max_filesize', '20M');
        ini_set('post_max_size', '20M');
        ini_set('max_execution_time', '300');
        ini_set('max_input_time', '300');

        // Osnovna validacija koja je uvek potrebna
        $validated = $request->validate([
            'category' => 'required|exists:categories,id',
            'subcategory' => 'required|numeric',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'serviceImages.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Dinamička validacija za pakete
        $packageRules = [];
        $availablePackages = ['basic', 'standard', 'premium'];

        // Proveravamo koje pakete je korisnik poslao
        $submittedPackages = [];
        foreach ($availablePackages as $package) {
            if ($request->has($package.'_price')) {
                $submittedPackages[] = $package;

                // Dodajemo pravila validacije za svaki prisutan paket
                $packageRules[$package.'_price'] = 'required|numeric|min:0';
                $packageRules[$package.'_delivery_days'] = 'required|integer|min:1';
                $packageRules[$package.'_inclusions'] = 'required|string';
            }
        }

        // Proveravamo da li je poslat barem jedan paket
        if (empty($submittedPackages)) {
            return back()->withErrors(['error' => 'Morate imati barem jedan paket.'])->withInput();
        }

        // Spajamo osnovnu i dinamičku validaciju
        $validated = $request->validate(array_merge([
            'category' => 'required|exists:categories,id',
            'subcategory' => 'required|numeric',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'serviceImages.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], $packageRules));

        $visible = 0;

        if(Auth::user()->package){
            $countPublicService = Service::where('user_id', Auth::id())->where('visible', 1)->count();
            if($countPublicService <= Auth::user()->package->quantity){
                $visible = filter_var($request->input('visible'), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }
        }

        try {
            // Priprema podataka za ažuriranje
            $serviceData = [
                'category_id' => $validated['category'],
                'subcategory_id' => $validated['subcategory'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'visible' => ($visible === 0 && $service->visible === null) ? null : $visible,
            ];

            if(Auth::user()->package && $visible === 1){
                $serviceData['visible_expires_at'] = now()->addMonth();
            }else{
                $serviceData['visible_expires_at'] = null;
            }

            // Ažuriramo podatke za svaki paket koji je poslat
            foreach ($availablePackages as $package) {
                if (in_array($package, $submittedPackages)) {
                    $serviceData[$package.'_price'] = $validated[$package.'_price'];
                    $serviceData[$package.'_delivery_days'] = $validated[$package.'_delivery_days'];
                    $serviceData[$package.'_inclusions'] = $validated[$package.'_inclusions'];
                } else {
                    // Ako paket nije poslat, postavljamo null vrednosti
                    $serviceData[$package.'_price'] = null;
                    $serviceData[$package.'_delivery_days'] = null;
                    $serviceData[$package.'_inclusions'] = null;
                }
            }

            // Ažuriranje servisa
            $service->update($serviceData);

            // Dodavanje novih slika (ostaje isto kao u originalnom kodu)
            if ($request->hasFile('serviceImages')) {
                $remainingSlots = 10 - $service->serviceImages()->count();

                if ($request->ajax() and $remainingSlots === 0) {
                    return response()->json([
                        'error' => 'Dostigli ste maksimalan broj slika (10) za ovaj servis !'
                    ]);
                }

                $maxSize = ini_get('upload_max_filesize');

                if ($remainingSlots > 0) {
                    $images = $request->file('serviceImages');
                    $images = array_slice($images, 0, $remainingSlots);
                    $uploadSuccess = true;
                    $errorMessage = '';

                    $directory = 'public/services';
                    if (!Storage::exists($directory)) {
                        Storage::makeDirectory($directory, 0755, true);
                    }

                    foreach ($images as $image) {
                        if ($image->getSize() > 2 * 1024 * 1024) {
                            continue;
                        }

                        $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                        try {
                            $image->storeAs($directory, $filename);

                            if (!Storage::exists($directory.'/'.$filename)) {
                                throw new \Exception("Slika nije sačuvana na disku");
                            }

                            $service->serviceImages()->create([
                                'service_id' => $service->id,
                                'image_path' => $filename
                            ]);

                        } catch (\Exception $e) {
                            $uploadSuccess = false;
                            $errorMessage = 'Došlo je do greške pri čuvanju slika: ' . $e->getMessage();
                            \Log::error('Image upload failed: ' . $e->getMessage());
                            break;
                        }
                    }

                    if (!$uploadSuccess) {
                        return redirect()->route('services.index', $service)
                            ->with('error', $errorMessage);
                    }
                } else {
                    return redirect()->route('services.index', $service)
                        ->with('error', 'Dostigli ste maksimalan broj slika (10) za ovaj servis.');
                }
            }

            if ($request->ajax()) {
                $successMessage = 'Ponuda '.$validated['title'].' je uspešno ažurirana.';
                $request->session()->flash('success', $successMessage);

                return response()->json([
                    'redirect' => route('services.index', $service)
                ]);
            }

            return redirect()->route('services.index', $service)
                ->with('success', 'Ponuda je uspešno ažurirana.');

        } catch (\Exception $e) {
            \Log::error('Service update error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Došlo je do greške prilikom ažuriranja servisa. Pokušajte ponovo.'])
                         ->withInput();
        }
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

    // Helper metoda za konverziju (npr. 20M u bajte)
    private function convertToBytes($size)
    {
        $unit = strtoupper(substr($size, -1));
        $value = (int)substr($size, 0, -1);

        switch ($unit) {
            case 'G': return $value * 1024 * 1024 * 1024;
            case 'M': return $value * 1024 * 1024;
            case 'K': return $value * 1024;
            default: return $value;
        }
    }
}
