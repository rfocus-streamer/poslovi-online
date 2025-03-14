<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DepositController;
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
