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
use App\Models\Invoice;
use App\Models\PrivilegedCommission;
use App\Providers\RouteServiceProvider;
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
        $user = Auth::user();

        // Izračunaj ukupan iznos rezervisanih sredstava sa dinamičkom provizijom
        $reserved_amount = Project::where('projects.buyer_id', Auth::id())
                            ->where(function ($query) {
                                $query->where('projects.seller_uncomplete_decision', '!=', 'accepted')
                                      ->orWhereNull('projects.seller_uncomplete_decision');
                            })
                            ->whereIn('projects.status', ['inactive', 'in_progress', 'waiting_confirmation', 'requires_corrections'])
                            ->join('commissions', 'projects.id', '=', 'commissions.project_id')
                            ->selectRaw('SUM(projects.reserved_funds + (projects.reserved_funds * commissions.buyer_percentage / 100)) as total_amount')
                            ->value('total_amount');

        $projects = [];

        if ($user->role == 'buyer') {
            $projects = Project::where('buyer_id', $user->id)->with('service', 'commission')->orderBy('created_at', 'desc')->get();
        } elseif ($user->role == 'seller') {
            $projects = Project::where('seller_id', $user->id)->with('service')->orderBy('created_at', 'desc')->get();
        }

        return view('projects.index', compact('projects', 'reserved_amount'));
    }

    public function view(Project $project)
    {
        $user = Auth::user();
        $reserved_amount = Project::where('buyer_id', Auth::id())->sum('reserved_funds');
        $service = Service::with([
            'user',
            'category',
            'subcategory',
            'serviceImages',
            'cartItems'
        ])->findOrFail($project->service_id);

        $title = $project->service->title;

        if(Auth::user()->role == 'seller')
        {
            // Izračunaj broj servisa za kupca
            $userServiceCount = Project::where('buyer_id', $project->buyer_id)
                ->where('status', 'completed')
                ->count();

            $userStars = $project->buyer->stars;

            $hasPendingRequest = (AdditionalCharge::where('seller_id', Auth::id())->where('project_id', $project->id)
                ->where('status', 'waiting_confirmation')
                ->count() > 0) ? true : false;

            $countReply = Complaint::where('user_id', Auth::id())->where('project_id', $project->id)->count();

            return view('projects.view_seller', compact(
                'title',
                'project',
                'service',
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

            $countReply = Complaint::where('user_id', $project->seller_id)->count();

            $hasPendingRequest = (AdditionalCharge::where('seller_id', $project->seller_id)->where('project_id', $project->id)
                ->where('status', 'waiting_confirmation')
                ->count() > 0) ? true : false;

            return view('projects.view', compact(
                'title',
                'project',
                'service',
                'reserved_amount',
                'userServiceCount',
                'userStars',
                'countReply'
            ));
        }
    }

    public function jobs()
    {
        if(Auth::user()->role !== 'seller')
        {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        $user = Auth::user();
        $reserved_amount = Project::where('buyer_id', Auth::id())->sum('reserved_funds');
        $projects = [];
        $totalEarnings = 0;

        if ($user->role == 'buyer') {
            $projects = Project::where('buyer_id', $user->id)->with('service', 'commission')->orderBy('created_at', 'desc')->get();
        } elseif ($user->role == 'seller' or $user->role == 'both') {
            $projects = Project::where('seller_id', $user->id)->with('service', 'buyer', 'commission')->orderBy('created_at', 'desc')->get();
        }

        if (Auth::check()) { // Proverite da li je korisnik ulogovan
            // Dohvati trenutni mesec i godinu
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $totalEarnings = Commission::where('seller_id', Auth::id())
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum(DB::raw('CASE WHEN seller_amount > 0 THEN amount - seller_amount ELSE 0 END'));
        }

        return view('projects.seller', compact(
            'projects',
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
        return redirect()->back()->with('success', 'Opis posla je uspešno ažuriran.');
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

        abort(403, 'Nemaš pravo pristupa ovom fajlu.');
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
            // Dohvati trenutno prijavljenog korisnika
            $user = Auth::user();

            if (empty($user->street) || empty($user->city) || empty($user->country)) {
                return redirect()->route('profile.edit')
                    ->with('error', 'Moraš da prvo popuniš ulicu, grad i zemlju pre nego što nastaviš sa kupovinom !');
            }

            // Proveri da li prodavac ima privilegovane komisije
            $privilegedCommission = PrivilegedCommission::where('user_id', $cart->seller_id)->first();

            // Postavi procente komisija na osnovu privilegija ili podrazumevane vrednosti
            $buyerCommissionPercentage = $privilegedCommission ? $privilegedCommission->buyer_commission : 3.00;
            $sellerCommissionPercentage = $privilegedCommission ? $privilegedCommission->seller_commission : 10.00;

            // Konvertujemo naziv paketa u mala slova
            $packageColumn = strtolower($cart->package) . '_price';

            // Dobijamo vrednost iz baze
            $reserved_funds = (float) Service::where('id', $cart->service_id)->value($packageColumn) ?? 0;

            $quantity = (int) $cart->quantity;

            // Sada kreiramo projekat
            $project = Project::create([
                'project_number' => 'pr_' . time() . '_' . Auth::id(),
                'service_id' => $cart->service_id,
                'quantity' => $quantity,
                'buyer_id' => Auth::id(),
                'seller_id' => $cart->seller_id,
                'package' => $cart->package,
                'status' => 'inactive',
                'reserved_funds' => ($reserved_funds * $quantity)
            ]);

            // Izračunavanje komisija na osnovu dinamičkih procenata
            $packageAmount = $reserved_funds;
            $buyerCommissionAmount = $packageAmount * ($buyerCommissionPercentage / 100);
            $totalAmount = $packageAmount * $quantity;
            $totalBuyerCommission = $buyerCommissionAmount * $quantity;

            // Dodavanje u tabelu za komisije
            Commission::create([
                'project_id' => $project->id,
                'project_number' => $project->project_number,
                'seller_id' => $cart->seller_id,
                'buyer_id' => Auth::id(),
                'amount' => $totalAmount,
                'seller_percentage' => $sellerCommissionPercentage,
                'buyer_percentage' => $buyerCommissionPercentage,
                'commission_amount' => $totalBuyerCommission,
                'seller_amount' => 0,
                'buyer_amount' => $totalBuyerCommission
            ]);

            // Umanji iznos iz deposits kolone
            $totalCost = $totalAmount + $totalBuyerCommission;
            $user->deposits -= $totalCost;
            $user->save();

            $cart->delete();
            return redirect()
                    ->back()
                    ->with('success', "Uspešno je pokrenut posao (projekat)")
                    ->withFragment('cart-message');
        } else {
            return redirect(RouteServiceProvider::HOME);
        }
    }

    public function acceptOffer(Project $project)
    {
        // Konvertujemo naziv paketa u mala slova
        $packageKey = strtolower($project->package);
        $deliveryColumn = $packageKey . '_delivery_days';
        $project->status = 'in_progress';
        $project->start_date = now();
        $project->end_date = now()->addDays($project->service->$deliveryColumn);
        $project->save();

        return redirect()
                    ->back()
                    ->with('success', "Uspešno je prihvaćen zahtev")
                    ->withFragment('project-message'); // Skrolujte do elementa sa ID "cart-message"
    }

    public function rejectOffer(Project $project)
    {
        if($project->status !== 'inactive'){
            return redirect()
                        ->back()
                        ->with('error', "Posao je već u toku, nije moguće otkazati !")
                        ->withFragment('project-message'); // Skrolujte do elementa sa ID
        }

        $user = Auth::user();
        if($user->role === 'seller'){
            $commission = Commission::where('project_number', $project->project_number)->first();
            $buyer_amount = 0;

            if ($commission) {
                $buyer_amount = $commission->buyer_amount;
                $commission->delete();
            }

            $project->status = 'rejected';
            $project->buyer->deposits += $project->reserved_funds + $buyer_amount;

            // Spremanje promena na korisniku
            $project->buyer->save();

            // Spremanje promena na projektu
            $project->save();

            return redirect()
                            ->back()
                            ->with('success', "Odbio si ovaj posao, rezervisana sredstva vraćaju se kupcu !")
                            ->withFragment('project-message'); // Skrolujte do elementa sa ID "cart-message"
        }else{
            $commission = Commission::where('project_number', $project->project_number)->first();
            $buyer_amount = 0;

            if ($commission) {
                $buyer_amount = $commission->buyer_amount;
                $commission->delete();
            }

            $project->buyer->deposits += $project->reserved_funds + $buyer_amount;

            // Spremanje promena na korisniku
            $project->buyer->save();

            // Spremanje promena na projektu
            $project->save();

            $project->delete();

            return redirect()
                            ->route('projects.index')
                            ->with('error', "Otkazao si ovaj posao, rezervisana sredstva su ti vraćena !")
                            ->withFragment('project-message'); // Skrolujte do elementa sa ID "cart-message"
        }
    }

    public function doneConfirmation(Project $project)
    {
        // Provera da li je projekat već završen
        if ($project->status === 'completed') {
            return redirect()
                      ->back()
                      ->with('error', 'Posao (projekat) je već završen.');
        }

        // Dohvati komisiju za ovaj projekat
        $commission = Commission::where('project_id', $project->id)->first();

        // Izračunaj iznose na osnovu dinamičkih procenata
        $packageAmount = $project->reserved_funds;
        $sellerCommissionPercentage = $commission->seller_percentage;

        $amountToAdd = $packageAmount * (1 - ($sellerCommissionPercentage / 100));
        $commissionAmount = $packageAmount * ($sellerCommissionPercentage / 100);

        // Dodavanje na deposits prodavca
        $project->seller->deposits += $amountToAdd;
        $project->seller->save();

        // Ažuriranje komisije
        $commission->increment('seller_amount', $commissionAmount);
        $commission->increment('commission_amount', $commissionAmount);

        $buyer_amount = $commission->buyer_amount;

        // Ažuriranje statusa projekta
        $project->status = 'completed';
        $project->save();

        $user = Auth::user();
        $invoice = Invoice::create([
            'number' => $project->project_number,
            'user_id' => $project->buyer_id,
            'issue_date' => now(),
            'status' => 'plaćen',
            'total' => ($packageAmount + $buyer_amount),
            'client_info' => [
                'name' => $user->firstname.' '.$user->lastname,
                'address' => $user->street,
                'city' => $user->city,
                'country' => $user->country
            ],
            'items' => [
                [
                    'description' => $project->service->title,
                    'billing_period' => 'Kupovina/završetak posla',
                    'quantity' => $project->quantity,
                    'amount' => $packageAmount,
                    'buyer_amount' => $buyer_amount,
                    'project_id' => $project->id
                ]
            ],
            'payment_method' => 'Deponovani iznos sa korisničkog računa'
        ]);

        return redirect()
                  ->back()
                  ->with('success', "Uspešno si potvrdio da je posao (projekat) završen!")
                  ->withFragment('project-message');
    }

    public function waitingConfirmation(Project $project)
    {
        $project->status = 'waiting_confirmation';
        $project->save();

        return redirect()
                    ->back()
                    ->with('success', "Uspešno si poslao zahtev za odobrenje završetka posla (projekta) !")
                    ->withFragment('project-message'); // Skrolujte do elementa sa ID "cart-message"
    }

    public function uncompleteConfirmationSeller(Project $project)
    {
        $project->status = 'uncompleted';
        $project->seller_uncomplete_decision = 'accepted';

        $project->buyer->deposits += $project->reserved_funds;

        // Spremanje promena na korisniku
        $project->buyer->save();

        // Spremanje promena na projektu
        $project->save();

        $buyer_commision = Commission::where('project_id', $project->id)->select('id', 'buyer_amount')->first();
        $buyer_amount = 0;

        if($buyer_commision){
            $buyer_amount = $buyer_commision->buyer_amount;
        }

        $user = $project->buyer;

        $invoice = Invoice::create([
            'number' => $project->project_number,
            'user_id' => $project->buyer_id,
            'issue_date' => now(),
            'status' => 'plaćen',
            'total' => $buyer_amount,
            'client_info' => [
                'name' => $user->firstname.' '.$user->lastname,
                'address' => $user->street,
                'city' => $user->city,
                'country' => $user->country
            ],
            'items' => [
                [
                    'description' => $project->service->title,
                    'billing_period' => 'Prodavac je prihvatio da je posao "nekompletiran", novac je refundiran ali je provizija naplaćena',
                    'quantity' => '',
                    'amount' => 0,
                    'buyer_amount' => $buyer_amount,
                    'project_id' => $project->id
                ]
            ],
            'payment_method' => 'Deponovani iznos sa korisničkog računa'
        ]);

        return redirect()
                    ->back()
                    ->with('success', "Tvoja odluka je sačuvana. Srećno u budućim poslovima!")
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

        $buyer_commision = Commission::where('project_id', $project->id)->select('id', 'buyer_amount')->first();
        $buyer_amount = 0;

        if($buyer_commision){
            $buyer_amount = $buyer_commision->buyer_amount;
        }

        $user = $project->buyer;

        $invoice = Invoice::create([
            'number' => $project->project_number,
            'user_id' => $project->buyer_id,
            'issue_date' => now(),
            'status' => 'plaćen',
            'total' => $buyer_amount,
            'client_info' => [
                'name' => $user->firstname.' '.$user->lastname,
                'address' => $user->street,
                'city' => $user->city,
                'country' => $user->country
            ],
            'items' => [
                [
                    'description' => $project->service->title,
                    'billing_period' => 'Arbitražni postupak u kome je refundiran novac jer je posao "nekompletiran"',
                    'quantity' => '',
                    'amount' => 0,
                    'buyer_amount' => $buyer_amount,
                    'project_id' => $project->id
                ]
            ],
            'payment_method' => 'Deponovani iznos sa korisničkog računa'
        ]);

        return redirect()
                    ->back()
                    ->with('success', "Tvoja odluka je sačuvana. Novac je prebačen kupcu!");
    }

    public function completeConfirmationSupport(Project $project)
    {
        $project->status = 'uncompleted';
        $project->admin_decision = 'accepted';

        // Dohvati komisiju za ovaj projekat
        $commission = Commission::where('project_id', $project->id)->first();
        $sellerCommissionPercentage = $commission->seller_percentage;

        // Izračunavanje iznosa na osnovu dinamičkih procenata
        $packageAmount = $project->reserved_funds;
        $amountToAdd = $packageAmount * (1 - ($sellerCommissionPercentage / 100));
        $commissionAmount = $packageAmount * ($sellerCommissionPercentage / 100);

        // Ažuriranje komisije
        $commission->increment('seller_amount', $commissionAmount);
        $commission->increment('commission_amount', $commissionAmount);

        $buyer_amount = $commission->buyer_amount;

        // Dodavanje na deposits prodavca
        $project->seller->deposits += $amountToAdd;
        $project->seller->save();

        $project->save();

        $user = $project->buyer;
        $invoice = Invoice::create([
            'number' => $project->project_number,
            'user_id' => $project->buyer_id,
            'issue_date' => now(),
            'status' => 'plaćen',
            'total' => ($packageAmount + $buyer_amount),
            'client_info' => [
                'name' => $user->firstname.' '.$user->lastname,
                'address' => $user->street,
                'city' => $user->city,
                'country' => $user->country
            ],
            'items' => [
                [
                    'description' => $project->service->title,
                    'billing_period' => 'Kupovina/završetak posla na osnovu procene podrške',
                    'quantity' => $project->quantity,
                    'amount' => $packageAmount,
                    'buyer_amount' => $buyer_amount,
                    'project_id' => $project->id
                ]
            ],
            'payment_method' => 'Deponovani iznos sa korisničkog računa'
        ]);

        return redirect()
                    ->back()
                    ->with('success', "Tvoja odluka je sačuvana. Novac je prebačen prodavcu!");
    }


    public function partiallyCompletedSupport(Request $request, Project $project)
    {
        $request->validate([
            'fairPlayAmount' => 'required|numeric|min:0|max:' . $project->reserved_funds,
        ]);

        // Dohvati komisiju za ovaj projekat
        $commission = Commission::where('project_id', $project->id)->first();
        $sellerCommissionPercentage = $commission->seller_percentage;

        $sellerAmount = $request->input('fairPlayAmount');
        $commissionAmount = $sellerAmount * ($sellerCommissionPercentage / 100);

        $buyer_commision = $commission->buyer_amount;
        $buyerAmount = ($project->reserved_funds - $sellerAmount) - $commissionAmount;

        DB::transaction(function () use ($project, $sellerAmount, $commissionAmount, $buyerAmount) {
            // Ažuriranje komisije
            $project->commission->increment('seller_amount', $sellerAmount);
            $project->commission->increment('commission_amount', $commissionAmount);

            // Ažuriranje statusa projekta
            $project->status = 'uncompleted';
            $project->admin_decision = 'partially';
            $project->save();

            // Ažuriranje balansa prodavca i kupca
            $project->seller->increment('deposits', $sellerAmount);
            $project->buyer->increment('deposits', $buyerAmount);
        });

        $user = $project->buyer;
        $invoice = Invoice::create([
            'number' => $project->project_number,
            'user_id' => $project->buyer_id,
            'issue_date' => now(),
            'status' => 'plaćen',
            'total' => ($sellerAmount + $commissionAmount + $buyer_commision),
            'client_info' => [
                'name' => $user->firstname.' '.$user->lastname,
                'address' => $user->street,
                'city' => $user->city,
                'country' => $user->country
            ],
            'items' => [
                [
                    'description' => $project->service->title,
                    'billing_period' => 'Delimično završetak posla na osnovu procene podrške',
                    'quantity' => $project->quantity,
                    'amount' => ($sellerAmount + $commissionAmount),
                    'buyer_amount' => $buyer_commision,
                    'project_id' => $project->id
                ]
            ],
            'payment_method' => 'Deponovani iznos sa korisničkog računa'
        ]);

        return redirect()
            ->back()
            ->with('success', 'Tvoja odluka je sačuvana. Novac je raspodeljen između prodavca i kupca.');
    }

    public function uncompleteConfirmationBuyer(Project $project)
    {
        $project->status = 'uncompleted';
        $project->seller_uncomplete_decision = null; // Postavljanje na null
        $project->save();

        return redirect()
                    ->back()
                    ->with('success', "Tvoja odluka je sačuvana da posao (projekat) nije završen!")
                    ->withFragment('project-message'); // Skrolujte do elementa sa ID "cart-message"
    }

    public function correctionConfirmationBuyer(Project $project)
    {
        $project->status = 'requires_corrections';
        $project->save();

        return redirect()
                    ->back()
                    ->with('success', "Tvoja odluka je sačuvana da projektu (poslu) treba korekcije!")
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
