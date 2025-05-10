<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\View\Composers\CommonDataComposer;
use App\Models\TicketResponse;
use App\Observers\TicketResponseObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        TicketResponse::observe(TicketResponseObserver::class);
        // Registruj Composer za 'layouts.app'
        View::composer('layouts.app', CommonDataComposer::class);
    }
}
