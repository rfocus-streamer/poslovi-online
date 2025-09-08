<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Package;
use Illuminate\Support\Facades\DB;
use PayPal\Api\WebhookEvent;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

class PayPalWebhookController extends Controller
{
    private $apiContext;

    public function __construct()
    {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                config('services.paypal.client_id'),
                config('services.paypal.secret')
            )
        );

        $this->apiContext->setConfig([
            'mode' => config('services.paypal.settings.mode'),
        ]);
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $headers = $request->headers->all();

        // Verifikacija webhook zahteva
        if (!$this->verifyWebhook($payload, $headers)) {
            Log::error('Neuspešna verifikacija PayPal webhook zahteva');
            return response()->json(['error' => 'Verifikacija neuspešna'], 400);
        }

        $event = json_decode($payload, true);

        if (!isset($event['event_type'])) {
            Log::error('Neispravan PayPal webhook payload');
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        Log::info('PayPal webhook primljen: '.$event['event_type']);

        switch ($event['event_type']) {
            case 'PAYMENT.SALE.COMPLETED':
                $this->handlePaymentCompleted($event['resource']);
                break;
            case 'BILLING.SUBSCRIPTION.ACTIVATED':
                $this->handleSubscriptionActivated($event['resource']);
                break;
            case 'BILLING.SUBSCRIPTION.CANCELLED':
                $this->handleSubscriptionCancelled($event['resource']);
                break;
            case 'BILLING.SUBSCRIPTION.EXPIRED':
                $this->handleSubscriptionExpired($event['resource']);
                break;
            default:
                Log::info('Neobrađen PayPal event: '.$event['event_type']);
        }

        return response()->json(['status' => 'success'], 200);
    }

    private function verifyWebhook($payload, $headers)
    {
        try {
            $webhookId = config('services.paypal.webhook_id');

            // Proverite da li su potrebni headeri prisutni
            if (!isset($headers['paypal-transmission-id'][0]) ||
                !isset($headers['paypal-transmission-time'][0]) ||
                !isset($headers['paypal-transmission-sig'][0]) ||
                !isset($headers['paypal-cert-url'][0])) {
                Log::error('Nedostaju potrebni headeri za verifikaciju webhook-a');
                return false;
            }

            $transmissionId = $headers['paypal-transmission-id'][0];
            $timestamp = $headers['paypal-transmission-time'][0];
            $signature = $headers['paypal-transmission-sig'][0];
            $certUrl = $headers['paypal-cert-url'][0];

            // Koristimo PayPal SDK za verifikaciju
            $webhookEvent = new WebhookEvent();
            $webhookEvent->setHeaders([
                'PAYPAL-TRANSMISSION-ID' => $transmissionId,
                'PAYPAL-TRANSMISSION-TIME' => $timestamp,
                'PAYPAL-TRANSMISSION-SIG' => $signature,
                'PAYPAL-CERT-URL' => $certUrl
            ]);

            $webhookEvent->setBody($payload);
            $verified = $webhookEvent->verify(
                $transmissionId,
                $timestamp,
                $webhookId,
                $signature,
                $certUrl,
                $payload,
                $this->apiContext
            );

            return $verified;
        } catch (\Exception $e) {
            Log::error('Greška pri verifikaciji PayPal webhook-a: ' . $e->getMessage());
            return false;
        }
    }

    private function handlePaymentCompleted(array $resource)
    {
        DB::transaction(function () use ($resource) {
            $saleId = $resource['id'];
            $billingAgreementId = $resource['billing_agreement_id'] ?? null;

            // Proveri da li transakcija već postoji
            $existingTransaction = Transaction::where('transaction_id', $saleId)->first();
            if ($existingTransaction) {
                Log::info("Transakcija je već obradjena: {$saleId}");
                return;
            }

            if (!$billingAgreementId) {
                Log::error('Nema billing agreement ID u completed payment');
                return;
            }

            $subscription = Subscription::where('subscription_id', $billingAgreementId)
                ->where('gateway', 'paypal')
                ->first();

            if (!$subscription) {
                Log::error("Pretplata nije pronađena za billing agreement: {$billingAgreementId}");
                return;
            }

            $user = $subscription->user;
            $amount = $resource['amount']['total'];
            $currency = $resource['amount']['currency'];

            // Kreiraj transakciju
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'subscription',
                'amount' => $amount,
                'currency' => $currency,
                'payment_method' => 'paypal',
                'transaction_id' => $saleId,
                'status' => 'completed',
                'payload' => json_encode($resource),
            ]);

            // Aktiviraj paket
            $package = Package::where('id', $subscription->plan_id)->first();
            if ($package) {
                app(\App\Http\Controllers\PackageController::class)->activatePackage($package, $user);
                Log::info("Paket aktiviran za korisnika {$user->id}: {$package->name}");
            } else {
                Log::error("Paket nije pronađen za PayPal plan: {$subscription->plan_id}");
            }

            // Ažuriraj status pretplate
            $subscription->update([
                'status' => 'active',
                'ends_at' => now()->addMonth(),
            ]);

            Log::info("PayPal plaćanje obradjeno za korisnika #{$user->id}, iznos: {$amount} {$currency}");
        });
    }

    private function handleSubscriptionActivated(array $resource)
    {
        $subscriptionId = $resource['id'];

        $subscription = Subscription::where('subscription_id', $subscriptionId)
            ->where('gateway', 'paypal')
            ->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'active',
                'ends_at' => now()->addMonth() // Podrazumevano produženje za 1 mesec
            ]);

            Log::info("PayPal pretplata aktivirana: {$subscriptionId}, važi do: " . now()->addMonth());
        } else {
            Log::error("PayPal pretplata nije pronađena: {$subscriptionId}");
        }
    }

    private function handleSubscriptionCancelled(array $resource)
    {
        $subscriptionId = $resource['id'];

        $subscription = Subscription::where('subscription_id', $subscriptionId)
            ->where('gateway', 'paypal')
            ->first();

        if ($subscription) {
            $subscription->update(['status' => 'canceled']);
            Log::info("PayPal pretplata otkazana: {$subscriptionId}");
        }
    }

    private function handleSubscriptionExpired(array $resource)
    {
        $subscriptionId = $resource['id'];

        $subscription = Subscription::where('subscription_id', $subscriptionId)
            ->where('gateway', 'paypal')
            ->first();

        if ($subscription) {
            $subscription->update(['status' => 'expired']);
            Log::info("PayPal pretplata istekla: {$subscriptionId}");
        }
    }
}
