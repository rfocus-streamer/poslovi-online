<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Service;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\CartItem;
use App\Models\ProjectFile;
use App\Models\AdditionalCharge;
use App\Models\Complaint;
use App\Models\Commission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get(); // Dohvati sve
        $user = Auth::user();
        $reserved_amount = Project::where('buyer_id', Auth::id())
                            ->where(function ($query) {
                                $query->where('seller_uncomplete_decision', '!=', 'accepted')
                                      ->orWhereNull('seller_uncomplete_decision');
                            })
                            ->sum('reserved_funds');

        $projects = [];
        $favoriteCount = 0;
        $cartCount = 0;
        $projectCount = 0;

        if ($user->role == 'buyer' or $user->role == 'both') {
            $projects = Project::where('buyer_id', $user->id)->with('service')->get();
        } elseif ($user->role == 'seller') {
            $projects = Project::where('seller_id', $user->id)->with('service')->get();
        }

        if (Auth::check()) { // Provera da li je korisnik ulogovan
            $favoriteCount = Favorite::where('user_id', Auth::id())->count();
            $cartCount = CartItem::where('user_id', Auth::id())->count();
            $projectCount = Project::where('buyer_id', Auth::id())->count();
        }

        return view('projects.index', compact('projects','categories', 'favoriteCount', 'cartCount', 'projectCount', 'reserved_amount'));
    }

    public function view(Project $project)
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get(); // Dohvati sve
        $user = Auth::user();
        $reserved_amount = Project::where('buyer_id', Auth::id())->sum('reserved_funds');
        $favoriteCount = 0;
        $cartCount = 0;

        $service = Service::with([
            'user',
            'category',
            'subcategory',
            'serviceImages',
            'cartItems'
        ])->findOrFail($project->service_id);

        if (Auth::check()) { // Provera da li je korisnik ulogovan
            $favoriteCount = Favorite::where('user_id', Auth::id())->count();
            $cartCount = CartItem::where('user_id', Auth::id())->count();
        }

        $title = $project->service->title;

        if(Auth::user()->role == 'seller')
        {
            // Izračunaj broj servisa za kupca
            $userServiceCount = Project::where('buyer_id', $project->buyer_id)
                ->where('status', 'completed')
                ->count();

            $userStars = $project->buyer->stars;

            $hasPendingRequest = (AdditionalCharge::where('seller_id', Auth::id())
                ->where('status', 'waiting_confirmation')
                ->count() > 0) ? true : false;

            $countReply = Complaint::where('user_id', Auth::id())->count();

            return view('projects.view_seller', compact(
                'title',
                'project',
                'categories',
                'service',
                'favoriteCount',
                'cartCount',
                'reserved_amount',
                'userServiceCount',
                'userStars',
                'hasPendingRequest',
                'countReply',
            ));
        }else{
            // Izračunaj broj servisa za tog sellera
            $userServiceCount = Service::where('user_id', $project->seller_id)->count();
            $userStars = $project->seller->stars;
            $projectCount = Project::where('buyer_id', Auth::id())->count();

            $countReply = Complaint::where('user_id', $project->seller_id)->count();

            return view('projects.view', compact(
                'title',
                'project',
                'projectCount',
                'categories',
                'service',
                'favoriteCount',
                'cartCount',
                'reserved_amount',
                'userServiceCount',
                'userStars',
                'countReply'
            ));
        }
    }

    public function jobs()
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get(); // Dohvati sve
        $user = Auth::user();
        $reserved_amount = Project::where('buyer_id', Auth::id())->sum('reserved_funds');
        $projects = [];
        $favoriteCount = 0;
        $cartCount = 0;
        $seller = [];
        $totalEarnings = 0;

        if ($user->role == 'buyer') {
            $projects = Project::where('buyer_id', $user->id)->with('service')->get();
        } elseif ($user->role == 'seller' or $user->role == 'both') {
            $projects = Project::where('seller_id', $user->id)->with('service', 'buyer')->get();
        }

        if (Auth::check()) { // Proverite da li je korisnik ulogovan
            $favoriteCount = Favorite::where('user_id', Auth::id())->count();
            $cartCount = CartItem::where('user_id', Auth::id())->count();
            $seller['countProjects'] = Project::where('seller_id', Auth::id())
                ->whereNotIn('status', ['completed', 'uncompleted'])
                ->count();
            // Dohvati trenutni mesec i godinu
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $totalEarnings = Commission::where('seller_id', Auth::id())
                                ->whereMonth('created_at', $currentMonth)
                                ->whereYear('created_at', $currentYear)
                                ->sum(DB::raw('amount - seller_amount'));
        }

        return view('projects.seller', compact(
            'projects',
            'seller',
            'categories',
            'favoriteCount',
            'cartCount',
            'reserved_amount',
            'totalEarnings'
        ));
    }

    /**
    * Ažurirajte opis projekta.
    */
    public function updateDescription(Request $request, Project $project)
    {
        // Validacija unosa
        $request->validate([
            'description' => 'required|string|max:2000', // Opis je obavezan i može imati do 2000 karaktera
        ]);

        // Ažuriranje opisa projekta
        $project->update([
            'description' => $request->input('description'),
        ]);

        // Redirekcija nazad sa porukom o uspehu
        return redirect()->back()->with('success', 'Opis projekta je uspešno ažuriran.');
    }

    public function upload(Request $request, Project $project)
    {
        $request->validate([
            'files.*' => 'required|file|max:10240', // Maksimalna veličina fajla 10MB
            'description' => 'nullable|string|max:255', // Opis je opcion i može imati do 255 karaktera
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // Kreiranje putanje u formatu project_{id}/filename
                $path = $file->store("project_files/project_{$project->id}", 'public');

                ProjectFile::create([
                    'project_id' => $project->id,
                    'user_id' => auth()->id(),
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'description' => $request->input('description'), // Dodajte opis
                ]);
            }
        }

        return redirect()->back()->with('success', 'Fajlovi su uspešno upload-ovani.');
    }

    public function downloadFile(ProjectFile $file): StreamedResponse
    {
        // Provera da li korisnik ima pravo pristupa fajlu
        $user = auth()->user();

        if($user->role === 'admin'){
            return Storage::disk('public')->download($file->file_path, $file->original_name);
        }

        // Provera da li korisnik ima pravo pristupa fajlu
        if (auth()->id() == $file->project->buyer_id || auth()->id() == $file->project->seller_id) {
           // Vraćanje fajla za preuzimanje
            return Storage::disk('public')->download($file->file_path, $file->original_name);
        }

        abort(403, 'Nemate pravo pristupa ovom fajlu.');
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
    public function store(CartItem $cart)
    {
        if (Auth::check()) {
           // Konvertujemo naziv paketa u mala slova
            $packageColumn = strtolower($cart->package) . '_price';

            // Dobijamo vrednost iz baze
            $reserved_funds = (float) Service::where('id', $cart->service_id)->value($packageColumn) ?? 0; // Koristimo float za broj

            $quantity = (int) $cart->quantity; // Osiguravamo da je quantity broj

            // Sada kreiramo projekat
            $project = Project::create([
                'project_number' => 'pr_' . time() . '_' . Auth::id(),
                'service_id' => $cart->service_id,
                'quantity' => $quantity,
                'buyer_id' => Auth::id(),
                'seller_id' => $cart->seller_id,
                'package' => $cart->package,
                'status' => 'inactive',
                'reserved_funds' => ($reserved_funds * $quantity) // Ovdje ne koristimo number_format, samo broj
            ]);

            // Izračunavanje 3% i 97% od ukupnog iznosa ( provizija i rezervisana sredstva )
            $packageAmount = $reserved_funds;
            $amountToAdd = $packageAmount * 0.97; // 97% za deposits prodavca
            $commissionAmount = $packageAmount * 0.03; // 3% za komisiju

            // Dodavanje 3% u tabelu za komisije kupca
            Commission::create([
                'project_id' => $project->id,
                'project_number' => $project->project_number,
                'seller_id' => $cart->seller_id,
                'buyer_id' =>Auth::id(),
                'amount' => $packageAmount, // Ukupan iznos projekta
                'seller_percentage' => 10, // Hardkodovani procenat provizije za prodavca
                'buyer_percentage' => 3, // Hardkodovani procenat provizije za kupca
                'commission_amount' => $commissionAmount, // 3% od ukupnog iznosa (inicijalno)
                'seller_amount' => 0, // prodavceva zarada od projekta
                'buyer_amount' => $commissionAmount // kupceva placena provizija
            ]);

            // Dohvati trenutno prijavljenog korisnika
            $user = Auth::user();

            // Umanji iznos iz deposits kolone
            $user->deposits -= ($reserved_funds * $cart->quantity)+$commissionAmount;
            $user->save();

            $cart->delete();
            return redirect()
                    ->back()
                    ->with('success', "Uspešno ste pokrenuli projekat")
                    ->withFragment('cart-message'); // Skrolujte do elementa sa ID "cart-message"
        }else{
            return redirect(RouteServiceProvider::HOME);
        }
    }

    public function acceptOffer(Project $project)
    {
        // Konvertujemo naziv paketa u mala slova
        $packageKey = strtolower($project->package);
        $packageColumn = $packageKey . '_price';
        $deliveryColumn = $packageKey . '_delivery_days';
        $project->reserved_funds = $project->service->$packageColumn;
        $project->status = 'in_progress';
        $project->start_date = now();
        $project->end_date = now()->addDays($project->service->$deliveryColumn);
        $project->save();

        return redirect()
                    ->back()
                    ->with('success', "Uspešno ste prihvatili zahtev")
                    ->withFragment('project-message'); // Skrolujte do elementa sa ID "cart-message"
    }

    public function rejectOffer(Project $project)
    {
        $commission = Commission::where('project_number', $project->project_number)->first();

        if ($commission) {
            $buyer_amount = $commission->buyer_amount;
            $commission->delete();
        }else{
            $buyer_amount = 0;
        }

        $project->status = 'rejected';
        $project->buyer->deposits += $project->reserved_funds + $buyer_amount;

        // Spremanje promena na korisniku
        $project->buyer->save();

        // Spremanje promena na projektu
        $project->save();

        return redirect()
                    ->back()
                    ->with('success', "Odbili ste ovaj projekat, rezervisana sredstva vracaju se kupcu !")
                    ->withFragment('project-message'); // Skrolujte do elementa sa ID "cart-message"
    }

    public function doneConfirmation(Project $project)
    {
        // Provera da li je projekat već završen
        if ($project->status === 'completed') {
            return redirect()
                      ->back()
                      ->with('error', 'Projekat je već završen.');
        }

        // Provera da li je provizija već obračunata
        if ($project->commission) {
            return redirect()
                      ->back()
                      ->with('error', 'Provizija za ovaj projekat je već obračunata.');
        }

        // Izračunavanje 10% i 90% od ukupnog iznosa ( prodavac - kupac )
        $packageColumn = strtolower($project->package) . '_price';
        $packageAmount = $project->service->$packageColumn;
        $amountToAdd = $packageAmount * 0.90; // 90% za deposits prodavca
        $commissionAmount = $packageAmount * 0.10; // 10% za komisiju

        // Dodavanje 90% na deposits prodavca
        $project->seller->deposits += $amountToAdd;

        // Spremanje promena na prodavcu
        $project->seller->save();

        // Dodavanje dodatnih 10% u tabelu za komisije ( kompletan projekat )
        Commission::where('project_id', $project->id)->increment('seller_amount', $commissionAmount);
        Commission::where('project_id', $project->id)->increment('commission_amount', $commissionAmount);

        // Ažuriranje statusa projekta na "completed"
        $project->status = 'completed';

        // Spremanje promena na projektu
        $project->save();

        return redirect()
                  ->back()
                  ->with('success', "Uspešno si potvrdio da je projekat završen!")
                  ->withFragment('project-message');
    }

    public function waitingConfirmation(Project $project)
    {
        $project->status = 'waiting_confirmation';
        $project->save();

        return redirect()
                    ->back()
                    ->with('success', "Uspešno ste poslali zahtev za odobrenje završetka posla (projekta) !")
                    ->withFragment('project-message'); // Skrolujte do elementa sa ID "cart-message"
    }

    public function uncompleteConfirmationSeller(Project $project)
    {
         $commission = Commission::where('project_number', $project->project_number)->first();

        if ($commission) {
            $buyer_amount = $commission->buyer_amount;
            $commission->delete();
        }else{
            $buyer_amount = 0;
        }

        $project->status = 'uncompleted';
        $project->seller_uncomplete_decision = 'accepted';

        $project->buyer->deposits += $project->reserved_funds + $buyer_amount;;

        // Spremanje promena na korisniku
        $project->buyer->save();

        // Spremanje promena na projektu
        $project->save();

        return redirect()
                    ->back()
                    ->with('success', "'Vaša odluka je sačuvana. Srećno u budućim poslovima!")
                    ->withFragment('project-message'); // Skrolujte do elementa sa ID "cart-message"
    }

    public function uncompleteConfirmationSupport(Project $project)
    {
        $project->status = 'uncompleted';
        $project->admin_decision = 'rejected';

        $project->buyer->deposits += $project->reserved_funds;

        // Spremanje promena na korisniku
        $project->buyer->save();

        // Spremanje promena na projektu
        $project->save();

        return redirect()
                    ->back()
                    ->with('success', "'Vaša odluka je sačuvana. Novac je prebačen kupcu!");
    }

    public function completeConfirmationSupport(Project $project)
    {
        $project->status = 'uncompleted';
        $project->admin_decision = 'accepted';

         // Izračunavanje 10% i 90% od ukupnog iznosa ( provizija i zarada prodavac )
        $packageAmount = $project->reserved_funds;
        $amountToAdd = $packageAmount * 0.90; // 90% za deposits prodavca
        $commissionAmount = $packageAmount * 0.10; // 10% za komisiju

         // Dodavanje dodatnih 10% u tabelu za komisije ( kompletan projekat )
        Commission::where('project_id', $project->id)->increment('seller_amount', $commissionAmount);
        Commission::where('project_id', $project->id)->increment('commission_amount', $commissionAmount);

        // Dodavanje 90% na deposits prodavca
        $project->seller->deposits += $amountToAdd;

        // Spremanje promena na korisniku
        $project->seller->save();

        // Spremanje promena na projektu
        $project->save();

        return redirect()
                    ->back()
                    ->with('success', "'Vaša odluka je sačuvana. Novac je prebačen prodavcu!");
    }

    public function partiallyCompletedSupport(Request $request, Project $project)
    {
        $request->validate([
            'fairPlayAmount' => 'required|numeric|min:0|max:' . $project->reserved_funds,
        ]);

        $sellerAmount = $request->input('fairPlayAmount');

         // Izračunavanje 10% i 90% od ukupnog iznosa ( provizija i zarada prodavac )
        $packageAmount = $sellerAmount;
        $commissionAmount = $packageAmount * 0.10; // 10% za komisiju

         // Dodavanje dodatnih 10% u tabelu za komisije ( kompletan projekat )
        Commission::where('project_id', $project->id)->increment('seller_amount', $commissionAmount);
        Commission::where('project_id', $project->id)->increment('commission_amount', $commissionAmount);


        $buyerAmount = $project->reserved_funds - $sellerAmount - $commissionAmount;

        DB::transaction(function () use ($project, $sellerAmount, $buyerAmount) {
            // Ažuriranje statusa projekta i odluke podrške
            $project->status = 'uncompleted';
            $project->admin_decision = 'partially';
            $project->save(); // OVDE sada pozivamo save unutar transakcije

            // Ažuriranje balansa prodavca i kupca
            $project->seller->increment('deposits', $sellerAmount);
            $project->buyer->increment('deposits', $buyerAmount);
        });

        return redirect()
            ->back()
            ->with('success', 'Vaša odluka je sačuvana. Novac je raspodeljen između prodavca i kupca.');
    }

    public function uncompleteConfirmationBuyer(Project $project)
    {
        $project->status = 'uncompleted';
        $project->seller_uncomplete_decision = null; // Postavljanje na null
        $project->save();

        return redirect()
                    ->back()
                    ->with('success', "Vaša odluka je sačuvana da projekat nije završen!")
                    ->withFragment('project-message'); // Skrolujte do elementa sa ID "cart-message"
    }

    public function correctionConfirmationBuyer(Project $project)
    {
        $project->status = 'requires_corrections';
        $project->save();

        return redirect()
                    ->back()
                    ->with('success', "Vaša odluka je sačuvana da projektu treba korekcije!")
                    ->withFragment('project-message'); // Skrolujte do elementa sa ID "cart-message"
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
    }
}
