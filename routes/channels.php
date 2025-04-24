<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// U channels.php (na samom početku)
//Broadcast::routes(['middleware' => ['api', 'auth:sanctum']]); // Za Sanctum

// Broadcast::channel('messages.{userId}', function ($user) {
//     return auth()->check(); // Samo autentifikovani korisnici
// });

// Autorizacija za chat kanal
Broadcast::channel('messages', function ($user) {
    return auth()->check(); // Samo autentifikovani korisnici
});

// Autorizacija za presence kanal (online status)
Broadcast::channel('presence-online-status', function ($user) {
    return ['id' => $user->id, 'is_online' => $user->is_online, 'last_seen_at' => $user->last_seen_at];
});

