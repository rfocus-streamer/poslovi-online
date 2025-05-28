<?php

namespace App\Services;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use App\Models\Package;
use GuzzleHttp\Client;

class PayPalService
{
    protected $client;
    protected $guzzle;
    protected $accessToken;
    protected $baseUrl;

    public function __construct()
    {
        // One-time payments (PayPal SDK)
        $clientId = config('services.paypal.client_id');
        $clientSecret = config('services.paypal.secret');
        $mode = config('services.paypal.settings.mode');

        $environment = $mode === 'live'
            ? new ProductionEnvironment($clientId, $clientSecret)
            : new SandboxEnvironment($clientId, $clientSecret);

        $this->client = new PayPalHttpClient($environment);

        // Subscriptions (Guzzle)
        $this->baseUrl = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        $this->guzzle = new Client([
            'base_uri' => $this->baseUrl,
        ]);

        $this->accessToken = $this->getAccessToken($clientId, $clientSecret);
    }

    // ✅ One-time Payment
    public function createPayment($amount, $currency, $description, $successUrl, $cancelUrl)
    {
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');

        $request->body = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "amount" => [
                    "currency_code" => $currency,
                    "value" => $amount
                ],
                "description" => $description
            ]],
            "application_context" => [
                "brand_name" => config('app.name'),
                "cancel_url" => $cancelUrl,
                "return_url" => $successUrl,
                "user_action" => "PAY_NOW"
            ]
        ];

        try {
            $response = $this->client->execute($request);
            return $response->result;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function executePayment($orderId)
    {
        $request = new OrdersCaptureRequest($orderId);

        try {
            $response = $this->client->execute($request);
            return $response->result;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    //Pretplata - subscription
    // 🔐 Get OAuth 2.0 access token
    protected function getAccessToken($clientId, $clientSecret)
    {
        try {
            $response = $this->guzzle->post('/v1/oauth2/token', [
                'auth' => [$clientId, $clientSecret],
                'form_params' => ['grant_type' => 'client_credentials'],
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['access_token'];
        } catch (RequestException $e) {
            throw new \Exception('PayPal token error: ' . $e->getMessage());
        }
    }

    // ✅ Kreiraj pretplatnički plan ako ne postoji
    public function createPlan(Package $package)
    {
        if ($package->paypal_plan_id) {
            return $package->paypal_plan_id; // Već postoji
        }

        // PayPal API endpoint
        $isLive = config('services.paypal.settings.mode') === 'live';
        $baseUrl = $isLive ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

        // 1. Dohvati Access Token
        $clientId = config('services.paypal.client_id');
        $clientSecret = config('services.paypal.secret');

        $client = new Client();

        $authResponse = $client->post("$baseUrl/v1/oauth2/token", [
            'auth' => [$clientId, $clientSecret],
            'form_params' => ['grant_type' => 'client_credentials']
        ]);

        $accessToken = json_decode($authResponse->getBody())->access_token;

        // 2. Kreiraj Product
        $productResponse = $client->post("$baseUrl/v1/catalogs/products", [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'name' => $package->name,
                'type' => 'SERVICE',
                'category' => 'SOFTWARE'
            ]
        ]);

        $productId = json_decode($productResponse->getBody())->id;

        // Izračunavanje naknade
        $paymentAmount = $package->price; // Originalni iznos
        $paypalFeePercentage = 4.99; // PayPal naknada u %
        $fixedFee = 0.30; // Fiksna naknada

        // Izračunaj koliko bi trebalo da kupac plati kako bi ti dobio željeni iznos
        $feeAmount = ($paymentAmount * ($paypalFeePercentage / 100)) + $fixedFee;
        $totalAmount = number_format(($paymentAmount + $feeAmount), 2, '.', '');

        // 3. Kreiraj Plan
        $planResponse = $client->post("$baseUrl/v1/billing/plans", [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'product_id' => $productId,
                'name' => $package->name,
                'description' => $package->description ?? $package->name,
                'billing_cycles' => [[
                    'frequency' => [
                        'interval_unit' => $package->duration === 'monthly' ? 'MONTH' : 'YEAR',
                        'interval_count' => 1
                    ],
                    'tenure_type' => 'REGULAR',
                    'sequence' => 1,
                    'total_cycles' => 0,
                    'pricing_scheme' => [
                        'fixed_price' => [
                            'value' => $totalAmount,
                            'currency_code' => 'EUR'
                        ]
                    ]
                ]],
                'payment_preferences' => [
                    'auto_bill_outstanding' => true,
                    'setup_fee_failure_action' => 'CONTINUE',
                    'payment_failure_threshold' => 1
                ]
            ]
        ]);

        $planId = json_decode($planResponse->getBody())->id;

        $package->paypal_plan_id = $planId;
        $package->save();

        return $planId;
    }


    // ✅ Kreiraj link za checkout
    public function createSubscriptionLink(string $planId, string $returnUrl, string $cancelUrl)
    {
        $response = $this->guzzle->post('/v1/billing/subscriptions', [
            'headers' => $this->headers(),
            'json' => [
                'plan_id' => $planId,
                'application_context' => [
                    'brand_name' => config('app.name'),
                    'locale' => 'en-US',
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'SUBSCRIBE_NOW',
                    'return_url' => $returnUrl,
                    'cancel_url' => $cancelUrl,
                ]
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    // ✅ Dohvatanje statusa pretplate
    public function getSubscription(string $subscriptionId)
    {
        $response = $this->guzzle->get("/v1/billing/subscriptions/{$subscriptionId}", [
            'headers' => $this->headers(),
        ]);

        return json_decode($response->getBody(), true);
    }

    // ✅ Otkazivanje pretplate
    public function cancelSubscription(string $subscriptionId)
    {
        return $this->guzzle->post("/v1/billing/subscriptions/{$subscriptionId}/cancel", [
            'headers' => $this->headers(),
            'json' => ['reason' => 'User requested cancellation'],
        ]);
    }

    protected function headers()
    {
        return [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json',
        ];
    }
}
