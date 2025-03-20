<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AdditionalChargeController;
use App\Http\Controllers\ComplaintController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [ServiceController::class, 'index'])->name('home');
Route::get('/ponuda/{id}', [ServiceController::class, 'show'])->name('services.show');

// Rute za logovane korisnike
Route::middleware('auth')->group(function () {
    Route::get('/favorites/search', [FavoriteController::class, 'search'])->name('favorites.search');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{service}', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{favorite}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{service}/{package}', [CartController::class, 'store'])->name('cart.store');
    Route::put('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/projects/{cart}', [ProjectController::class, 'store'])->name('projects.store');
    Route::post('/projects/{project}/accept', [ProjectController::class, 'acceptOffer']);

    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/jobs', [ProjectController::class, 'jobs'])->name('projects.jobs');
    Route::get('/projects/{project}/view', [ProjectController::class, 'view'])->name('projects.view');
    Route::get('/project-file/{file}', [ProjectController::class, 'downloadFile'])->name('project.file.download')->middleware('auth');
    Route::post('/projects/{project}/accept', [ProjectController::class, 'acceptOffer'])->name('projects.acceptoffer');
    Route::post('/projects/{project}/waitingconfirmation', [ProjectController::class, 'waitingConfirmation'])->name('projects.waitingconfirmation');
    Route::post('/projects/{project}/confirmationuncompleteseller', [ProjectController::class, 'uncompleteConfirmationSeller'])->name('projects.confirmationuncompleteseller');
    Route::post('/projects/{project}/confirmationuncompletebuyer', [ProjectController::class, 'uncompleteConfirmationBuyer'])->name('projects.confirmationuncompletebuyer');
    Route::post('/projects/{project}/confirmationdone', [ProjectController::class, 'doneConfirmation'])->name('projects.confirmationdone');
    Route::post('/projects/{project}/upload', [ProjectController::class, 'upload'])->name('projects.upload');
    Route::put('/projects/{project}/update-description', [ProjectController::class, 'updateDescription'])
    ->name('projects.updateDescription');

    // Rute za dodatnu naplatu
    Route::get('/projects/{project}/additional-charges/create', [AdditionalChargeController::class, 'create'])
        ->name('additional_charges.create');
    Route::post('/projects/{project}/additional-charges', [AdditionalChargeController::class, 'store'])
        ->name('additional_charges.store');
    Route::get('/projects/{project}/additional-charges', [AdditionalChargeController::class, 'index'])
        ->name('additional_charges.index');

    // Rute za prigovore
    Route::get('/projects/{project}/complaints', [ComplaintController::class, 'create'])
        ->name('complaints.create');
    Route::post('/projects/{project}/complaints', [ComplaintController::class, 'store'])
        ->name('complaints.store');
    Route::put('/complaints/{complaint}', [ComplaintController::class, 'update'])
        ->name('complaints.update');

    Route::get('/deposit', [DepositController::class, 'create'])->name('deposit.create');
    Route::post('/deposit', [DepositController::class, 'store'])->name('deposit.store');
});

// Social Login rute
Route::prefix('login')->group(function () {
    Route::get('/google', [App\Http\Controllers\Auth\SocialLoginController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/google/callback', [App\Http\Controllers\Auth\SocialLoginController::class, 'handleGoogleCallback']);

    Route::get('/facebook', [App\Http\Controllers\Auth\SocialLoginController::class, 'redirectToFacebook'])->name('login.facebook');
    Route::get('/facebook/callback', [App\Http\Controllers\Auth\SocialLoginController::class, 'handleFacebookCallback']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
