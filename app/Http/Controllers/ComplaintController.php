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

        // Provera da li je korisnik prodavac i da li je status projekta 'uncompleted'
        if (auth()->id() !== $project->seller_id || $project->status !== 'uncompleted') {
            abort(403, 'Nemate pravo da podnesete prigovor.');
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
        // Provera da li je korisnik prodavac i da li je status projekta 'uncompleted'
        if (auth()->id() !== $project->seller_id || $project->status !== 'uncompleted') {
            abort(403, 'Nemate pravo da podnesete prigovor.');
        }

        // Validacija unosa
        $request->validate([
            'message' => 'required|string|max:1000',
            'attachment' => 'nullable|file|max:10240', // Maksimalna veličina fajla 10MB
        ]);

        // Čuvanje prigovora
        $complaint = new Complaint([
            'message' => $request->input('message')
        ]);

        // Čuvanje priloga (ako postoji)
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('complaints', 'public');
            $complaint->attachment = $path;
        }

        $countReply = Complaint::where('user_id', Auth::id())->count();

        // Poveži prigovor sa projektom i prodavcem
        $complaint->project()->associate($project);
        $complaint->participant()->associate(auth()->user());
        $complaint->save();

        if($countReply > 0)
        {
            $project->admin_decision_reply = 'disabled';
            $project->seller_uncomplete_decision = 'arbitration';
            $project->save();
        }

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
