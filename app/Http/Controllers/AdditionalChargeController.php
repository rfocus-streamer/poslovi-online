<?php

namespace App\Http\Controllers;

use App\Models\AdditionalCharge;
use App\Models\Project;
use Illuminate\Http\Request;

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

        // Redirekcija na rutu services.show sa service_id
        return redirect()->route('services.show', $project->service->id)
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
}
