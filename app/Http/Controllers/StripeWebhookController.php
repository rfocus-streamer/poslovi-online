<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Package;
use App\Services\StripeService;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            Log::error('Stripe Webhook - Invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            Log::error('Stripe Webhook - Invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Log successful verification
        Log::info('Stripe Webhook - Event received', ['type' => $event->type]);

        switch ($event->type) {
            case 'invoice.payment_succeeded':
                $this->handleInvoicePaymentSucceeded($event->data->object);
                break;
            case 'invoice.paid':
                $this->handleInvoicePaid($event->data->object);
                break;
            case 'charge.refunded':
                $this->handleChargeRefunded($event->data->object);
                break;
            // Dodajte druge event tipove po potrebi
        }

        return response()->json(['status' => 'success']);
    }

    private function handleInvoicePaid($invoice)
    {
        try {
            \DB::transaction(function () use ($invoice) {
                // Pronađi pretplatu
                $subscription = Subscription::where('subscription_id', $invoice->subscription)->first();

                if (!$subscription) {
                    Log::error('Subscription not found for paid invoice: '.$invoice->subscription);
                    return;
                }

                // Pronađi korisnika
                $user = User::find($subscription->user_id);

                if (!$user) {
                    Log::error('User not found for subscription: '.$subscription->id);
                    return;
                }

                // Proveri da li je transakcija već obradena
                $existingTransaction = Transaction::where('transaction_id', $invoice->payment_intent)->first();
                if ($existingTransaction) {
                    Log::info('Transaction already processed: '.$invoice->payment_intent);
                    return;
                }

                // Kreiraj transakciju
                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'subscription',
                    'amount' => $invoice->amount_paid / 100,
                    'currency' => strtoupper($invoice->currency),
                    'payment_method' => 'stripe',
                    'transaction_id' => $invoice->payment_intent,
                    'status' => 'completed',
                    'payload' => json_encode($invoice)
                ]);

                // Aktiviraj paket
                $package = Package::where('id', $subscription->plan_id)->first();
                if ($package) {
                    app(\App\Http\Controllers\PackageController::class)->activatePackage($package);
                    Log::info("Package activated for user {$user->id}: {$package->name}");

                    // Ažuriraj status pretplate
                    $subscription->update([
                        'status' => 'active',
                        'ends_at' => now()->addMonth(), // ili koliko plan traje
                    ]);

                    Log::info('Webhook - Subscription ID: ' . $subscriptionId);
                    Log::info('Webhook - Plan ID: ' . $subscription->plan_id);
                } else {
                    Log::error('Package not found for user {$user->id} for subscription: '.$subscription->id);
                }

                Log::info("Subscription payment processed for user {$user->id}");
            });
        } catch (\Exception $e) {
            Log::error('Payment processing error: '.$e->getMessage());
        }
    }

    private function handleInvoicePaymentSucceeded($invoice)
    {
        try {
            \DB::transaction(function () use ($invoice) {
                // Pronađi pretplatu
                $subscription = Subscription::where('subscription_id', $invoice->subscription)->first();

                if (!$subscription) {
                    Log::error('Subscription not found: '.$invoice->subscription);
                    return;
                }

                // Proveri da li je invoice već plaćen
                if (!$invoice->paid) {
                    Log::info('Invoice not paid yet: '.$invoice->id);
                    return;
                }

                // Pronađi korisnika
                $user = User::find($subscription->user_id);

                // Proveri da li je transakcija već obradena
                $existingTransaction = Transaction::where('transaction_id', $invoice->payment_intent)->first();
                if ($existingTransaction) {
                    Log::info('Transaction already processed: '.$invoice->payment_intent);
                    return;
                }

                // Kreiraj transakciju
                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'subscription',
                    'amount' => $invoice->amount_paid / 100,
                    'currency' => strtoupper($invoice->currency),
                    'payment_method' => 'stripe',
                    'transaction_id' => $invoice->payment_intent,
                    'status' => 'completed',
                    'payload' => json_encode($invoice)
                ]);

                // Aktiviraj paket
                $package = Package::find($subscription->plan_id);
                if ($package) {
                    app(\App\Http\Controllers\PackageController::class)->activatePackage($package);
                    Log::info("Package activated for user {$user->id}: {$package->name}");
                } else {
                    Log::error('Package not found for subscription: '.$subscription->id);
                }

                Log::info("Subscription payment processed for user {$user->id}");
            });
        } catch (\Exception $e) {
            Log::error('Payment processing error: '.$e->getMessage());
        }
    }

    private function handleChargeRefunded($charge)
    {
        // Implementirajte po potrebi
    }
}
