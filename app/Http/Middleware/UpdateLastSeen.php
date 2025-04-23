<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UpdateLastSeen
{
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            auth()->user()->updateLastSeen();
        }
        return $next($request);
    }

    public function terminate($request, $response)
    {
        if (Auth::check()) {
            Auth::user()->update(['is_online' => false]);
        }
    }
}
