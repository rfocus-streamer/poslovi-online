<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Package;
use Symfony\Component\HttpFoundation\Response;

class PayPalWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $headers = [
            'auth_algo' => $request->header('PAYPAL-AUTH-ALGO'),
            'cert_url' => $request->header('PAYPAL-CERT-URL'),
            'transmission_id' => $request->header('PAYPAL-TRANSMISSION-ID'),
            'transmission_sig' => $request->header('PAYPAL-TRANSMISSION-SIG'),
            'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME'),
        ];

        $webhookId = config('services.paypal.webhook_id');

        // U development okruženju, preskoči verifikaciju ako je postavljen flag
        if (config('app.env') === 'local' && config('services.paypal.skip_verification')) {
            Log::warning('PayPal Webhook - Preskačem verifikaciju u development okruženju');
        } else {
            // Proveri da li webhook zaista dolazi od PayPala
            if (!$this->verifyWebhookSignature($payload, $headers, $webhookId)) {
                Log::error('PayPal Webhook - Nevažeći potpis');
                return response()->json(['error' => 'Nevažeći potpis'], 400);
            }
        }

        $eventType = $request->event_type;
        $resource = $request->resource;

        Log::info('PayPal Webhook - Primljen događaj', ['type' => $eventType]);

        switch ($eventType) {
            case 'BILLING.SUBSCRIPTION.ACTIVATED':
            case 'BILLING.SUBSCRIPTION.RENEWED':
                $this->handleSubscriptionRenewed($resource);
                break;
            case 'PAYMENT.SALE.COMPLETED':
                $this->handlePaymentCompleted($resource);
                break;
            case 'BILLING.SUBSCRIPTION.CANCELLED':
                $this->handleSubscriptionCancelled($resource);
                break;
            // Dodajte druge tipove događaja po potrebi
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Proverava da li webhook zaista dolazi od PayPala
     * Verifikacija digitalnog potpisa
     */
    private function verifyWebhookSignature($payload, $headers, $webhookId)
    {
        $clientId = config('services.paypal.client_id');
        $clientSecret = config('services.paypal.secret');
        $mode = config('services.paypal.mode');

        $url = $mode == 'live'
            ? 'https://api.paypal.com/v1/notifications/verify-webhook-signature'
            : 'https://api.sandbox.paypal.com/v1/notifications/verify-webhook-signature';

        $data = [
            'auth_algo' => $headers['auth_algo'],
            'cert_url' => $headers['cert_url'],
            'transmission_id' => $headers['transmission_id'],
            'transmission_sig' => $headers['transmission_sig'],
            'transmission_time' => $headers['transmission_time'],
            'webhook_id' => $webhookId,
            'webhook_event' => json_decode($payload, true)
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($clientId . ':' . $clientSecret)
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200) {
            $responseData = json_decode($response, true);
            return $responseData['verification_status'] === 'SUCCESS';
        }

        Log::error('PayPal Webhook verifikacija neuspešna', ['response' => $response]);
        return false;
    }

    private function handleSubscriptionRenewed($resource)
    {
        try {
            \DB::transaction(function () use ($resource) {
                $subscription = Subscription::where('subscription_id', $resource->id)->first();

                if (!$subscription) {
                    Log::error('PayPal pretplata nije pronađena: '.$resource->id);
                    return;
                }

                // Ažuriraj detalje pretplate
                $subscription->update([
                    'status' => 'active',
                    'ends_at' => now()->addMonth(), // Prilagodi prema billing ciklusu
                ]);

                Log::info("PayPal pretplata obnovljena: {$resource->id}");
            });
        } catch (\Exception $e) {
            Log::error('Greška pri obnovi PayPal pretplate: '.$e->getMessage());
        }
    }

    private function handlePaymentCompleted($resource)
    {
        try {
            \DB::transaction(function () use ($resource) {
                // Pronađi pretplatu povezanu sa ovom uplatom
                $subscription = Subscription::where('subscription_id', $resource->billing_agreement_id)->first();

                if (!$subscription) {
                    Log::error('PayPal pretplata nije pronađena za uplatu: '.$resource->id);
                    Log::error('PayPal resource: '.$resource);
                    return;
                }

                $user = User::find($subscription->user_id);

                // Proveri da li transakcija već postoji
                $existingTransaction = Transaction::where('transaction_id', $resource->id)->first();
                if ($existingTransaction) {
                    Log::info('PayPal transakcija već obrađena: '.$resource->id);
                    return;
                }

                // Kreiraj zapis o transakciji
                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'subscription',
                    'amount' => $resource->amount->total,
                    'currency' => $resource->amount->currency,
                    'payment_method' => 'paypal',
                    'transaction_id' => $resource->id,
                    'status' => 'completed',
                    'payload' => json_encode($resource)
                ]);

                // Aktiviraj paket
                $package = Package::find($subscription->plan_id);
                if ($package) {
                    app(\App\Http\Controllers\PackageController::class)->activatePackage($user, $package);
                    Log::info("Paket aktiviran za korisnika {$user->id}: {$package->name}");
                }

                Log::info("PayPal uplata obrađena za korisnika {$user->id}");
            });
        } catch (\Exception $e) {
            Log::error('Greška pri obradi PayPal uplate: '.$e->getMessage());
        }
    }

    private function handleSubscriptionCancelled($resource)
    {
        try {
            \DB::transaction(function () use ($resource) {
                $subscription = Subscription::where('subscription_id', $resource->id)->first();

                if (!$subscription) {
                    Log::error('PayPal pretplata nije pronađena za otkazivanje: '.$resource->id);
                    return;
                }

                // Ažuriraj status pretplate na otkazano
                $subscription->update([
                    'status' => 'cancelled',
                ]);

                Log::info("PayPal pretplata otkazana: {$resource->id}");
            });
        } catch (\Exception $e) {
            Log::error('Greška pri otkazivanju PayPal pretplate: '.$e->getMessage());
        }
    }

    /**
     * Testna metoda za proveru PayPal webhook integracije
     * Ovo je samo za testiranje - ne koristiti u produkciji
     */
    public function testWebhook(Request $request)
    {
        if (config('app.env') !== 'local') {
            return response()->json(['error' => 'Ova metoda je dostupna samo u lokalnom okruženju'], 403);
        }

        // Simuliraj PayPal webhook payload
        $testPayload = [
            'id' => 'WH-TEST-123',
            'event_type' => 'BILLING.SUBSCRIPTION.RENEWED',
            'resource' => [
                'id' => 'I-BW452GLLEP1G', // Test subscription ID
                'billing_agreement_id' => 'B-123456789', // Test billing agreement
                'amount' => [
                    'total' => '9.99',
                    'currency' => 'USD'
                ]
            ],
            'create_time' => now()->toIso8601String()
        ];

        // Simuliraj PayPal headers
        $testHeaders = [
            'PAYPAL-AUTH-ALGO' => 'test',
            'PAYPAL-CERT-URL' => 'test',
            'PAYPAL-TRANSMISSION-ID' => 'test-'.uniqid(),
            'PAYPAL-TRANSMISSION-SIG' => 'test',
            'PAYPAL-TRANSMISSION-TIME' => now()->toIso8601String()
        ];

        // Kreiraj mock request
        $mockRequest = Request::create(
            '/webhook/paypal',
            'POST',
            [],
            [],
            [],
            $testHeaders,
            json_encode($testPayload)
        );

        // Obradi testni webhook
        return $this->handleWebhook($mockRequest);
    }

    /**
     * Ping metoda za testiranje konekcije sa PayPal serverom
     */
    public function pingPayPal()
    {
        try {
            $clientId = config('services.paypal.client_id');
            $clientSecret = config('services.paypal.secret');
            $mode = config('services.paypal.mode');

            // Proverite da li su ključevi postavljeni
            if (empty($clientId) || empty($clientSecret)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'PayPal ključevi nisu konfigurisani'
                ], 500);
            }

            $url = $mode == 'live'
                ? 'https://api.paypal.com/v1/oauth2/token'
                : 'https://api.sandbox.paypal.com/v1/oauth2/token';

            // Izgradi curl komandu
            $command = sprintf(
                'curl -v %s -H "Accept: application/json" -H "Accept-Language: en_US" -u "%s:%s" -d "grant_type=client_credentials"',
                $url,
                $clientId,
                $clientSecret
            );

            // Izvrši komandu
            $output = [];
            $returnCode = 0;
            exec($command . ' 2>&1', $output, $returnCode);

            // Procesiraj rezultat
            $combinedOutput = implode("\n", $output);

            // Pokušaj da ekstrahuješ JSON deo iz odgovora
            $jsonResponse = null;
            if (preg_match('/\{.*\}/s', $combinedOutput, $matches)) {
                $jsonResponse = json_decode($matches[0], true);
            }

            return response()->json([
                'status' => $returnCode === 0 ? 'success' : 'error',
                'command' => str_replace($clientSecret, '***', $command), // Maskiraj secret u logu
                'return_code' => $returnCode,
                'output' => $combinedOutput,
                'json_response' => $jsonResponse
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Greška pri testiranju konekcije: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Testna metoda za verifikaciju webhook potpisa
     */
    public function testWebhookVerification()
    {
        try {
            $webhookId = config('services.paypal.webhook_id');

            // Koristite stvarne testne podatke od PayPala
            // Ovo možete dobiti iz PayPal Developer Dashboard-a
            $testPayload = json_encode([
                'event_type' => 'BILLING.SUBSCRIPTION.RENEWED',
                'id' => 'WH-TEST-123',
                'resource' => [
                    'id' => 'I-BW452GLLEP1G'
                ]
            ]);

            // Koristite stvarne headere od PayPala
            $testHeaders = [
                'auth_algo' => 'test',
                'cert_url' => 'test',
                'transmission_id' => uniqid(),
                'transmission_sig' => 'test',
                'transmission_time' => now()->toIso8601String()
            ];

            // U development okruženju, preskoči verifikaciju
            if (config('app.env') === 'local' && config('services.paypal.skip_verification')) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Webhook verifikacija preskočena u development okruženju',
                    'verified' => true
                ]);
            }

            $verified = $this->verifyWebhookSignature($testPayload, $testHeaders, $webhookId);

            return response()->json([
                'status' => 'success',
                'message' => 'Webhook verifikacija testirana',
                'verified' => $verified
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Greška pri testiranju webhook verifikacije: ' . $e->getMessage()
            ], 500);
        }
    }
}
