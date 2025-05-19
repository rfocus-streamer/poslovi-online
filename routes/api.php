<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Admin\DashboardController;

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
    Route::get('/get-last-message-service', [MessageController::class, 'getLastMessageByService'])->name('get.lastmessagesbyservice');
    Route::get('/get-messages-complaints', [MessageController::class, 'getMessagesComplaints'])->name('get.messagesComplaints');
    Route::post('/messages/block/{user}', [MessageController::class, 'blockUser'])->name('messages.blockUser');
    Route::post('/messages/unblock/{user}', [MessageController::class, 'unblockUser'])->name('messages.unblockUser');

    Route::get('/admin/{user}/profile', [DashboardController::class, 'profile'])->name('admin.user.profile');
    Route::patch('/profile/{user}', [DashboardController::class, 'updateProfile'])->name('admin.profile.update');

    Route::get('/admin/{user}/deposit', [DashboardController::class, 'deposit'])->name('admin.user.deposit');
    Route::delete('admin/{user}', [DashboardController::class, 'destroy'])->name('admin.user.destroy');

    Route::delete('/admin/files/delete', [DashboardController::class, 'deleteFile'])->name('files.delete');
    // ... ostale rute
});


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
