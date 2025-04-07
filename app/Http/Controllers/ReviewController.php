<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Review;
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
            return redirect()->back()->with('error', 'Već ste ocenili ovu uslugu.');
        }

        Review::create([
            'service_id' => $project->service_id,
            'user_id' => Auth::id(),
            'comment' => $validated['comment'],
            'rating' => $request->rating
        ]);

        return redirect()->back()->with('success', 'Hvala na recenziji!');
    }
}
