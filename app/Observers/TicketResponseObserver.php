<?php

namespace App\Observers;

use App\Models\TicketResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class TicketResponseObserver
{
    public function retrieved(TicketResponse $response)
    {
        // Proverite da li smo na show ruti za ticket
        $isOnTicketShowRoute = Route::currentRouteName() === 'tickets.show';

        // Označi odgovor kao pročitan samo ako:
        // - Nije već pročitan
        // - Nije napisao trenutni korisnik
        // - Nalazimo se na show ruti
        if ($isOnTicketShowRoute &&
            Auth::check() &&
            $response->user_id !== Auth::id() &&
            is_null($response->read_at)) {

            $response->updateQuietly(['read_at' => now()]);
        }
    }

    /**
     * Handle the TicketResponse "created" event.
     */
    public function created(TicketResponse $ticketResponse): void
    {
        //
    }

    /**
     * Handle the TicketResponse "updated" event.
     */
    public function updated(TicketResponse $ticketResponse): void
    {
        //
    }

    /**
     * Handle the TicketResponse "deleted" event.
     */
    public function deleted(TicketResponse $ticketResponse): void
    {
        //
    }

    /**
     * Handle the TicketResponse "restored" event.
     */
    public function restored(TicketResponse $ticketResponse): void
    {
        //
    }

    /**
     * Handle the TicketResponse "force deleted" event.
     */
    public function forceDeleted(TicketResponse $ticketResponse): void
    {
        //
    }
}
