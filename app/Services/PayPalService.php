<?php

namespace App\Services;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

class PayPalService
{
    protected $client;

    public function __construct()
    {
        $clientId = config('services.paypal.client_id');
        $clientSecret = config('services.paypal.secret');
        $mode = config('services.paypal.settings.mode');

        $environment = $mode === 'live'
            ? new ProductionEnvironment($clientId, $clientSecret)
            : new SandboxEnvironment($clientId, $clientSecret);

        $this->client = new PayPalHttpClient($environment);
    }

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

    //Pretplata
    public function createPlan(Package $package)
    {
        $plan = new Plan();
        $plan->setName($package->name)
             ->setDescription($package->description)
             ->setType('INFINITE');

        $paymentDefinition = new PaymentDefinition();
        $paymentDefinition->setName('Regular Payments')
            ->setType('REGULAR')
            ->setFrequency(strtoupper($package->duration === 'monthly' ? 'MONTH' : 'YEAR'))
            ->setFrequencyInterval('1')
            ->setCycles('0')
            ->setAmount(new Currency(['value' => $package->price, 'currency' => $package->currency]));

        $merchantPreferences = new MerchantPreferences();
        $merchantPreferences->setReturnUrl(config('app.url'))
            ->setCancelUrl(config('app.url'))
            ->setAutoBillAmount('yes')
            ->setInitialFailAmountAction('CONTINUE')
            ->setMaxFailAttempts('0');

        $plan->addPaymentDefinition($paymentDefinition);
        $plan->setMerchantPreferences($merchantPreferences);

        $createdPlan = $plan->create($this->apiContext);
        return $createdPlan;
    }

    public function createAgreement($planId, $successUrl, $cancelUrl)
    {
        $agreement = new Agreement();
        $agreement->setName('Subscription Agreement')
            ->setDescription('Auto-Renewing Subscription')
            ->setStartDate(now()->addMinutes(5)->toIso8601String());

        $plan = new Plan();
        $plan->setId($planId);
        $agreement->setPlan($plan);

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $agreement->setPayer($payer);

        $agreement = $agreement->create($this->apiContext);
        return $agreement;
    }

    public function executeAgreement($token)
    {
        $agreement = new Agreement();
        $agreement->execute($token, $this->apiContext);
        return $agreement;
    }
}
