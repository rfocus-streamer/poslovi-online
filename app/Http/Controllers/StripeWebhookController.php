<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Subscription;
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
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );
        } catch (\Exception $e) {
            Log::error('Stripe webhook error: '.$e->getMessage());
            return response()->json(['error' => 'Invalid signature'], Response::HTTP_BAD_REQUEST);
        }
        //$event = json_decode($payload); // ovo nam sluzi samo za test preko postman-a

        switch ($event->type) {
            case 'invoice.payment_succeeded':
                $this->handleInvoicePaymentSucceeded($event->data->object);
                break;

            case 'charge.refunded':
                $this->handleChargeRefunded($event->data->object);
                break;
        }

        return response()->json(['status' => 'success']);
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

                // Pronađi korisnika
                $user = User::where('id',$subscription->user_id)->first();

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

                // Ažuriraj balans
                $user->deposits += $invoice->amount_paid / 100;
                $user->save();

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
