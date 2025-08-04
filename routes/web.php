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
use App\Http\Controllers\PackageController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\PayPalWebhookController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Mews\Captcha\Facades\Captcha;
use Illuminate\Support\Str;
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
Route::get('/api/load-more-services', [ServiceController::class, 'loadMoreServices'])->name('api.load-more');
Route::get('/ponuda/{id}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/verify/{id}/{hash}', [RegisteredUserController::class, 'verify'])
    ->name('verification.email');

// Rute za logovane korisnike
Route::middleware('auth')->group(function () {
    Route::get('/services', [ServiceController::class, 'sellerServices'])->name('services.index');
    Route::get('/services/{service}', [ServiceController::class, 'viewServices'])->name('services.view');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::get('/service/new', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/service/new', [ServiceController::class, 'store'])->name('services.store');
    Route::delete('/service/image/{image}', [ServiceController::class, 'deleteServiceImage'])->name('services.image.delete');
    Route::delete('/service/{service}', [ServiceController::class, 'destroy'])->name('services.delete');

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
    Route::post('/projects/{project}/reject', [ProjectController::class, 'rejectOffer'])->name('projects.rejectoffer');
    Route::post('/projects/{project}/waitingconfirmation', [ProjectController::class, 'waitingConfirmation'])->name('projects.waitingconfirmation');
    Route::post('/projects/{project}/confirmationuncompleteseller', [ProjectController::class, 'uncompleteConfirmationSeller'])->name('projects.confirmationuncompleteseller');
    Route::post('/projects/{project}/confirmationuncompletebuyer', [ProjectController::class, 'uncompleteConfirmationBuyer'])->name('projects.confirmationuncompletebuyer');

    Route::post('/projects/{project}/confirmationuncompletesupport', [ProjectController::class, 'uncompleteConfirmationSupport'])->name('projects.confirmationuncompletesupport');
    Route::post('/projects/{project}/confirmationcompletesupport', [ProjectController::class, 'completeConfirmationSupport'])->name('projects.confirmationcompletesupport');
    Route::post('/projects/{project}/partiallycompletedsupport', [ProjectController::class, 'partiallyCompletedSupport'])->name('projects.partiallycompletedsupport');

    Route::post('/projects/{project}/confirmationcorrectionbuyer', [ProjectController::class, 'correctionConfirmationBuyer'])->name('projects.confirmationcorrectionbuyer');
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
    Route::post('/projects/{charge}/accept-charge', [AdditionalChargeController::class, 'accept'])
        ->name('additional_charges.accept');
    Route::post('/projects/{charge}/reject-charge', [AdditionalChargeController::class, 'reject'])
        ->name('additional_charges.reject');

    // Rute za prigovore
    Route::get('/complaints/', [ComplaintController::class, 'index'])
        ->name('complaints.index');
    Route::get('/projects/{project}/complaints', [ComplaintController::class, 'show'])
        ->name('complaints.show');
    Route::post('/projects/{project}/complaints', [ComplaintController::class, 'store'])
        ->name('complaints.store');
    Route::put('/complaints/{complaint}', [ComplaintController::class, 'update'])
        ->name('complaints.update');

    Route::get('/deposit', [DepositController::class, 'showDepositForm'])->name('deposit.form');
    Route::post('/deposit/paypal/create', [DepositController::class, 'createPayment'])->name('deposit.create');
    Route::get('/deposit/paypal/success', [DepositController::class, 'payPalSuccess'])->name('deposit.paypal.success');
    Route::get('/deposit/paypal/cancel', [DepositController::class, 'payPalCancel'])->name('deposit.paypal.cancel');

    Route::post('/deposit/stripe/create', [DepositController::class, 'createStripePayment'])->name('deposit.stripe.create');
    Route::get('/deposit/stripe/success', [DepositController::class, 'stripeSuccess'])->name('deposit.stripe.success');
    Route::get('/deposit/stripe/cancel', [DepositController::class, 'stripeCancel'])->name('deposit.stripe.cancel');

    Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
    Route::patch('/package/{package}', [PackageController::class, 'activatePackage'])->name('package.activate');

    Route::post('/affiliate/payout', [AffiliateController::class, 'requestPayout'])->name('affiliate.payout');

    Route::post('/reviews/{project}', [ReviewController::class, 'store'])->name('reviews.store');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/send-message', [MessageController::class, 'send'])->name('send.message');
    Route::post('/mark-as-read', [MessageController::class, 'markAsRead'])->name('messages.read');

    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoice/{id}', [InvoiceController::class, 'getPDF'])->name('invoice.download');

    Route::get('/affiliate', [AffiliateController::class, 'index'])->name('affiliate.index');
    Route::post('/affiliate/activate', [AffiliateController::class, 'activateAffiliate'])->name('affiliate-activate');

    //Tickets
    Route::resource('tickets', TicketController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('/tickets/{ticket}/redirect', [TicketController::class, 'redirectToTeam'])
         ->name('tickets.redirect');

    Route::post('/tickets/{ticket}/responses', [TicketController::class, 'storeResponse'])
         ->name('tickets.responses.store');
    Route::post('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])
     ->name('tickets.update-status');
    Route::post('/tickets/responses/{response}/mark-as-read', [TicketController::class, 'markAsRead'])
    ->name('tickets.responses.mark-as-read');

    // Subscriptions
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');

    // Kreiranje nove pretplate
    Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');

    // PayPal success/cancel
    Route::get('/paypal/success', [SubscriptionController::class, 'paypalSuccess'])->name('paypal.success');
    Route::patch('/subscriptions/{subscription}/paypal-cancel', [SubscriptionController::class, 'paypalCancel'])->name('subscription.paypal.cancel');

    // Stripe success/cancel
    Route::get('/subscription/stripe/success', [SubscriptionController::class, 'stripeSuccess'])->name('subscription.stripe.success');
    Route::patch('/subscription/stripe/cancel/{subscription}', [SubscriptionController::class, 'stripeCancel'])->name('subscription.stripe.cancel');

    Route::get('/subscription/{id}/details', [SubscriptionController::class, 'details'])->name('subscription.details');
    Route::delete('/subscriptions/{id}', [SubscriptionController::class, 'destroy'])
    ->name('subscription.destroy');

    // Webhookovi
    Route::post('/webhook/paypal', [SubscriptionController::class, 'handlePayPalWebhook']);

    // Admin route
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('forced-services', [ForcedServiceController::class, 'index'])
        ->name('admin.forced-services.index');

    Route::post('forced-services/update', [DashboardController::class, 'updateForcedServices'])
        ->name('dashboard.forced-services.update');

});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::post('/paypal/webhook', [PayPalWebhookController::class, 'handleWebhook'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
// Route::post('/stripe/webhook', [PayPalWebhookController::class, 'handleWebhook'])
//     ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// routes/web.php (privremena test ruta)
// Route::get('/generate-webhook', function() {
//     $payload = json_encode([
//         'id' => 'evt_test_' . Str::random(10),
//         'type' => 'invoice.payment_succeeded',
//         'data' => [
//             'object' => [
//                 'id' => 'in_test_' . Str::random(8),
//                 'amount_paid' => 999,
//                 'currency' => 'eur',
//                 'subscription' => 'sub_' . Str::random(14)
//             ]
//         ]
//     ]);

//     $secret = config('services.stripe.webhook_secret');
//     $timestamp = time();
//     $signature = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);

//     return response()->json([
//         'payload_to_send' => json_decode($payload),
//         'headers_to_use' => [
//             'Stripe-Signature' => "t=$timestamp,v1=$signature",
//             'Content-Type' => 'application/json'
//         ]
//     ]);
// });

Route::get('/attachments/{file}', function ($file) {
    $path = storage_path('app/attachments/'.$file);

    if (!File::exists($path)) {
        abort(404);
    }

    return response()->file($path);
})->where('file', '.*')->middleware('auth'); // Zaštita pristupa


Route::get('/check-auth', function () {
    return auth()->check() ? "Autentifikovan" : "Nije autentifikovan";
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
    Route::patch('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.changePassword');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('update-role', [ProfileController::class, 'UpdateRole'])->name('profile.updaterole');
});

Route::get('/terms', function () {
    // Generišemo URL za PDF fajl u storage/public/pdf
    $fileUrl = asset('storage/pdf/USLOVI-KORIŠĆŠENJA.pdf');

    // Preusmeravamo korisnika na URL (ako želite da PDF bude direktno preuzet)
    return redirect($fileUrl);
})->name('terms');

Route::get('/privacy-policy', function () {
    $fileUrl = asset('storage/pdf/POLITIKA-PRIVATNOSTI.pdf');

    return redirect($fileUrl);
})->name('privacy-policy');

Route::get('/cookies', function () {
    $fileUrl = asset('storage/pdf/POLITIKA-KOLAČIĆA.pdf');

    return redirect($fileUrl);
})->name('cookies');

Route::get('/affiliate-contract', function () {
    $fileUrl = asset('storage/pdf/Affiliate-Program.pdf');

    return redirect($fileUrl);
})->name('affiliate-contract');

// Generisanje matematičke CAPTCHA-e
Route::get('/math-captcha', function () {
    $operator = ['+', '-', '*'][rand(0, 2)]; // Nasumično biramo operator

    // Generišemo brojeve na način koji sprečava negativne rezultate
    if ($operator === '-') {
        $num1 = rand(5, 15);      // Veći opseg za prvi broj
        $num2 = rand(1, $num1);   // Drugi broj je uvek manji ili jednak prvom
    } else {
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
    }

    // Izračunavamo rezultat
    switch($operator) {
        case '+': $result = $num1 + $num2; break;
        case '-': $result = $num1 - $num2; break;
        case '*': $result = $num1 * $num2; break;
    }

    session()->put('math_captcha', $result);

    return response()->json([
        'question' => "Izračunaj rezultat: $num1 $operator $num2 ="
    ]);
})->name('captcha');

require __DIR__.'/auth.php';
