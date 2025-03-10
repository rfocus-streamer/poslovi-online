<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSellerSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next) {
        $user = $request->user();
        if ($user->role !== 'seller' && $user->role !== 'both') {
            return redirect()->route('home');
        }

        $subscription = Subscription::where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->first();

        if (!$subscription) {
            return redirect()->route('seller.subscribe')->with('error', 'Nemaš aktivnu pretplatu!');
        }

        return $next($request);
    }
}
