<?php

namespace App\Http\Controllers;

use App\Models\AdditionalCharge;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdditionalChargeController extends Controller
{
    /**
     * Prikaz forme za dodatnu naplatu.
     */
    public function create(Project $project)
    {
        // Provera da li je korisnik prodavac
        if (auth()->id() !== $project->seller_id) {
            abort(403, 'Samo prodavac može dodati dodatnu naplatu.');
        }

        return view('projects.additional_charge_form', compact('project'));
    }

    /**
     * Čuvanje dodatne naplate.
     */
    public function store(Request $request, Project $project)
    {
        // Provera da li je korisnik prodavac
        if (auth()->id() !== $project->seller_id) {
            abort(403, 'Samo prodavac može dodati dodatnu naplatu.');
        }

        // Validacija unosa
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:1000',
        ]);

        // Kreiranje dodatne naplate
        AdditionalCharge::create([
            'project_id' => $project->id,
            'seller_id' => auth()->id(),
            'amount' => $request->input('amount'),
            'reason' => $request->input('reason')
        ]);

        // Redirekcija na rutu projects.view
        return redirect()->route('projects.view', $project->id)
                    ->with('success', 'Zahtev za dodatnom naplatom je uspešno poslat.');
    }

    /**
     * Prikaz svih zahteva za dodatnu naplatu.
     */
    public function index(Project $project)
    {
        $additionalCharges = $project->additionalCharges;
        return view('projects.additional_charges', compact('project', 'additionalCharges'));
    }

    public function accept(Request $request, AdditionalCharge $charge)
    {
        $project = Project::where('id', $charge->project_id)->first();
        // Dohvati trenutno prijavljenog korisnika
        $user = Auth::user();

        if($project)
        {
            $amount = $charge->amount;
            $project->reserved_funds += $amount;
            $project->save();

            $user->deposits -= $amount;
            $user->save();

            $charge->status = 'completed';
            $charge->save();

            return redirect()->route('projects.view', $project->id)
                    ->with('success', 'Prihvatio si dodatnu naplatu. Odgovarajući iznos je skinut sa tvog depozita i prebačen u rezervisana sredstva ovog projekta.');
        }

        return redirect()->route('projects.view', $project->id)
                    ->with('error', 'Došlo je do greške prilikom prebacivanja za dodatnu naplatu !');
    }

    public function reject(Request $request, AdditionalCharge $charge)
    {
        $charge->status = 'rejected';
        $charge->save();
        return redirect()->route('projects.view', $charge->project->id)
                    ->with('success', 'Odbio si dodatnu naplatu. Iznos ostaje na tvom depozitu i neće biti prebačen u rezervisana sredstva.');
    }
}
