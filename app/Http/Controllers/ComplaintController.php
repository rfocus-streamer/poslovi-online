<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Project;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get();

        $query = Project::with(['complaints', 'seller', 'service']);

        // Primena uslova samo ako nije pretraga
        if (!$request->has('search') || empty($request->search)) {
            $query->where('seller_uncomplete_decision', 'arbitration');
        }

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('service', function($serviceQuery) use ($searchTerm) {
                    $serviceQuery->where('title', 'like', "%$searchTerm%");
                })
                ->orWhereHas('seller', function($sellerQuery) use ($searchTerm) {
                    $sellerQuery->where('firstname', 'like', "%$searchTerm%")
                               ->orWhere('lastname', 'like', "%$searchTerm%");
                })
                ->orWhereHas('complaints', function($complaintQuery) use ($searchTerm) {
                    $complaintQuery->where('description', 'like', "%$searchTerm%");
                });
            });
        }

        $complaints = $query->paginate(25);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('complaints.support', compact('categories', 'complaints'))->render()
            ]);
        }

        return view('complaints.support', compact('categories', 'complaints'));
    }

    /**
     * Prikaz forme za podnošenje prigovora.
     */
    public function create(Project $project)
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get(); // Dohvati sve
        $user = Auth::user();
        $reserved_amount = Project::where('buyer_id', Auth::id())->sum('reserved_funds');
        $projects = [];
        $favoriteCount = 0;
        $cartCount = 0;

        if($user->role !== 'support')
        {
            // Provera da li je korisnik prodavac i da li je status projekta 'uncompleted'
            if (auth()->id() !== $project->seller_id || $project->status !== 'uncompleted') {
                abort(403, 'Nemate pravo da podnesete prigovor.');
            }
        }

        if (Auth::check()) { // Proverite da li je korisnik ulogovan
            $favoriteCount = Favorite::where('user_id', Auth::id())->count();
            $cartCount = CartItem::where('user_id', Auth::id())->count();
        }

        return view('complaints.index', compact('project','categories', 'favoriteCount', 'cartCount', 'reserved_amount'));
    }

    /**
     * Čuvanje prigovora.
     */
    public function store(Request $request, Project $project)
    {
        $user = Auth::user();
        if($user->role !== 'support')
        {
            // Provera da li je korisnik prodavac i da li je status projekta 'uncompleted'
            if (auth()->id() !== $project->seller_id || $project->status !== 'uncompleted') {
                abort(403, 'Nemate pravo da podnesete prigovor.');
            }
        }

        // Validacija unosa
        $request->validate([
            'message' => 'required|string|max:1000',
            'attachment' => 'nullable|file|max:10240', // Maksimalna veličina fajla 10MB
        ]);

        // Čuvanje prigovora
        $complaint = new Complaint([
            'message' => $request->input('message'),
            'service_id' => $project->service->id
        ]);

        // Čuvanje priloga (ako postoji)
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('complaints', 'public');
            $complaint->attachment = $path;
        }

        // Poveži prigovor sa projektom i prodavcem
        $complaint->project()->associate($project);
        $complaint->participant()->associate(auth()->user());
        $complaint->service()->associate($project->service);
        $complaint->save();

        $countReply = Complaint::where('user_id', Auth::id())->count();

        if($countReply > 0)
        {
            $project->seller_uncomplete_decision = 'arbitration';
        }

        $enablereply = $request->has('enablereply') ? 1 : 0;
        if($user->role === 'support' and $enablereply === 1)
        {
            $project->admin_decision_reply = 'enabled';
        }else{
            $project->admin_decision_reply = 'disabled';
        }

        $project->save();

        return redirect()->route('complaints.create', $project)
            ->with('success', 'Prigovor je uspešno podnet.');
    }

    /**
     * Ažuriranje statusa prigovora od strane podrške.
     */
    public function update(Request $request, Complaint $complaint)
    {
        // Provera da li je korisnik admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Samo admin može ažurirati status prigovora.');
        }

        // Validacija unosa
        $request->validate([
            'status' => 'required|in:in_progress,rejected,completed',
            'admin_decision' => 'nullable|string|max:1000',
        ]);

        // Ažuriranje prigovora
        $complaint->update([
            'status' => $request->input('status'),
            'admin_decision' => $request->input('admin_decision'),
        ]);

        return redirect()->back()->with('success', 'Status prigovora je uspešno ažuriran.');
    }
}
