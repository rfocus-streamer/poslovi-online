<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:api')->group(function () {
//     Route::post('/messages', [MessageController::class, 'store']);
    //Route::put('/messages/{message}/read', [MessageController::class, 'markAsRead']);
//     Route::get('/unread-count', fn () => response()->json(['count' => auth()->user()->unread_messages_count]));
//     Route::post('/activity-ping', function () {
//         auth()->user()->update([
//             'last_seen_at' => now(),
//             'is_online' => true
//         ]);
//         return response()->json();
//     });
// });

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/messages/{message}/read', [MessageController::class, 'markAsRead']);
    Route::get('/get-messages', [MessageController::class, 'getMessages'])->name('get.messages');
});


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
