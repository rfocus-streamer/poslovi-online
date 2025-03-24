<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

class StripeService
{
    protected $stripe;

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function createPayment($amount, $currency, $paymentMethodId, $returnUrl = null)
    {
        try {
            $params = [
                'amount' => $this->convertToStripeFormat($amount, $currency),
                'currency' => strtolower($currency),
                'payment_method' => $paymentMethodId,
                'confirm' => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never'
                ],
                'metadata' => [
                    'user_id' => auth()->id()
                ]
            ];

            if ($returnUrl) {
                $params['return_url'] = $returnUrl;
            }

            return PaymentIntent::create($params);
        } catch (ApiErrorException $e) {
            throw new \Exception('Payment failed: ' . $e->getMessage());
        }
    }

    /**
     * Dohvatanje PaymentIntent-a sa Stripe-a
     */
    public function retrievePaymentIntent(string $paymentIntentId)
    {
        try {
            return \Stripe\PaymentIntent::retrieve($paymentIntentId);
        } catch (ApiErrorException $e) {
            Log::error('Stripe retrieve error: ' . $e->getMessage());
            throw new \Exception('Unable to retrieve payment status');
        }
    }

    private function convertToStripeFormat($amount, $currency)
    {
        $zeroDecimalCurrencies = ['JPY', 'KRW', 'VND', 'RSD'];

        if (!in_array(strtoupper($currency), $zeroDecimalCurrencies)) {
            return $amount * 100;
        }

        return $amount;
    }
}
