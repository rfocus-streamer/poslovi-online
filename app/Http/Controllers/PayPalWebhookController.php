<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class PayPalWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $event = json_decode($payload, true);

        if (!isset($event['event_type'])) {
            Log::error('Neispravan PayPal webhook payload');
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        Log::info('PayPal webhook primljen: '.$event['event_type']);

        switch ($event['event_type']) {
            case 'BILLING.SUBSCRIPTION.ACTIVATED':
                $this->handleSubscriptionActivated($event['resource']);
                break;

            case 'BILLING.SUBSCRIPTION.CANCELLED':
                $this->handleSubscriptionCancelled($event['resource']);
                break;

            // Dodaj još ako ti trebaju drugi eventovi
        }

        return response()->json(['status' => 'success'], 200);
    }

    private function handleSubscriptionActivated(array $resource)
    {
        DB::transaction(function () use ($resource) {
            $subscriptionId = $resource['id'];
            $subscription = Subscription::where('subscription_id', $subscriptionId)
                ->where('gateway', 'paypal')
                ->first();

            if (!$subscription) {
                Log::error("Pretplata nije pronađena: {$subscriptionId}");
                return;
            }

            $user = $subscription->user;

            // Kreiraj transakciju
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'subscription',
                'amount' => $subscription->amount,
                'currency' => 'EUR',
                'payment_method' => 'paypal',
                'transaction_id' => $subscriptionId,
                'status' => 'completed',
                'payload' => json_encode($resource),
            ]);

            // Ažuriraj depozit
            $user->increment('deposits', $subscription->amount);

            // Ažuriraj status pretplate
            $subscription->update([
                'status' => 'active',
                'ends_at' => now()->addMonth(), // ili koliko plan traje
            ]);

            Log::info("PayPal pretplata aktivirana za korisnika #{$user->id}");
        });
    }

    private function handleSubscriptionCancelled(array $resource)
    {
        $subscriptionId = $resource['id'];

        $subscription = Subscription::where('subscription_id', $subscriptionId)
            ->where('gateway', 'paypal')
            ->first();

        if ($subscription) {
            $subscription->update(['status' => 'canceled']);
            Log::info("Pretplata otkazana: {$subscriptionId}");
        }
    }
}
