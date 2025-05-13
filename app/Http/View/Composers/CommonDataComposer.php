<?php

namespace App\Http\View\Composers;

use App\Models\Category;
use App\Models\Project;
use App\Models\Favorite;
use App\Models\CartItem;
use App\Models\Service;
use App\Models\Message;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class CommonDataComposer
{
    public function compose(View $view)
    {
        // Kategorije
        $categories = Category::with('subcategories')->whereNull('parent_id')->get();

        // Korisnički podaci
        $user = Auth::user();
        $reserved_amount = Project::where('buyer_id', Auth::id())
                                    ->where(function ($query) {
                                        $query->where('seller_uncomplete_decision', '!=', 'accepted')
                                              ->orWhereNull('seller_uncomplete_decision');
                                    })
                                    ->sum('reserved_funds');

        // Početni brojači
        $favoriteCount = 0;
        $cartCount = 0;
        $projectCount = 0;
        $seller = [];
        $messagesCount = 0;
        $messagesCount = 0;

        if (Auth::check()) {
            $favoriteCount = Favorite::where('user_id', Auth::id())->count();
            $cartCount = CartItem::where('user_id', Auth::id())->count();
            $projectCount = Project::where('buyer_id', Auth::id())->count();
            $seller['countProjects'] = Project::where('seller_id', Auth::id())
                ->whereNotIn('status', ['completed', 'uncompleted'])
                ->count();
            $seller['countPublicService'] = Service::where('user_id', Auth::id())
                ->where('visible', 1)
                ->count();
            $messagesCount = Message::where('receiver_id', Auth::id())
                                ->where('read_at', null)
                                ->count();

            // Broj otvorenih žalbi
            $complaintCount = Project::where('seller_uncomplete_decision', 'arbitration')
                ->whereHas('complaints')
                ->count();
        }

        // Data koja će biti dostupna svim prikazima
        $view->with(compact('categories', 'favoriteCount', 'cartCount', 'projectCount', 'seller', 'reserved_amount', 'messagesCount', 'complaintCount'));
    }
}
