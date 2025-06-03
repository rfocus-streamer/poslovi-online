<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Project $project)
    {

        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        // Check if user already reviewed this service
        $existingReview = Review::where('user_id', Auth::id())
                                ->where('service_id', $project->service_id)
                                ->first();
        if ($existingReview) {
            return redirect()->back()->with('error', 'Već si ocenio ovu uslugu.');
        }

        Review::create([
            'service_id' => $project->service_id,
            'user_id' => Auth::id(),
            'comment' => $validated['comment'],
            'rating' => $request->rating
        ]);

        // Pribavi servis i vlasnika servisa
        $service = Service::findOrFail($project->service_id);
        $owner = $service->user; // koristi relaciju user() iz Service modela

        // Pribavi sve servise koje taj korisnik (vlasnik) poseduje
        $ownerServiceIds = $owner->services()->pluck('id');

        // Izračunaj prosečnu ocenu za sve te servise
        $averageRating = Review::whereIn('service_id', $ownerServiceIds)->avg('rating');

        // Zaokruži i ograniči vrednost ocene između 1 i 5
        $rounded = round($averageRating ?? 0);
        $owner->stars = max(1, min(5, $rounded));

        // Sačuvaj ažuriranu ocenu
        $owner->save();

        return redirect()->back()->with('success', 'Hvala na recenziji!');
    }
}
