<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Subscription as StripeSubscription;
use Stripe\StripeClient;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use App\Models\Package;
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

    //Pretplata
    public function createCustomer($user)
    {
        return \Stripe\Customer::create([
            'email' => $user->email,
            'name' => $user->name,
        ]);
    }

    public function createPrice(Package $package)
    {
        $productId = $package->stripe_product_id;

        if (!$productId) {
            $product = $this->createOrFindProduct($package);
            $productId = $product->id;
            $package->stripe_product_id = $productId;
            $package->save();
        }

        // Proveri da li već postoji odgovarajući Price
        $prices = \Stripe\Price::all([
            'product' => $productId,
            'active' => true,
        ]);

         // Izračunavanje naknade
        $paymentAmount = $package->price; // Originalni iznos
        $stripeFeePercentage = 2.9; // Stripe naknada u %
        $fixedFee = 0.30; // Fiksna naknada

        // Izračunaj koliko bi trebalo da kupac plati kako bi ti dobio željeni iznos
        $feeAmount = ($paymentAmount * ($stripeFeePercentage / 100)) + $fixedFee;
        $totalAmount = number_format(($paymentAmount + $feeAmount), 2, '.', '');

        return \Stripe\Price::create([
            'product' => $productId,
            'unit_amount' => $totalAmount * 100,
            'currency' => 'eur',
            'recurring' => [
                'interval' => $package->duration === 'monthly' ? 'month' : 'year',
            ],
        ]);
    }


    private function createProduct(Package $package)
    {
        return \Stripe\Product::create([
            'name' => $package->name,
            'description' => $package->description
        ]);
    }


    public function createSubscriptionCheckout($priceId, $customerId, $successUrl, $cancelUrl)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $successUrlWithSession = $successUrl . (str_contains($successUrl, '?') ? '&' : '?') . 'session_id={CHECKOUT_SESSION_ID}';

        return \Stripe\Checkout\Session::create([
            'customer' => $customerId,
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $priceId,
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => $successUrlWithSession,
            'cancel_url' => $cancelUrl,
        ]);
    }

    private function createOrFindProduct(Package $package)
    {
        // Stripe Product Search je još uvek u beta fazi, koristi metadata za lakšu identifikaciju
        $existing = \Stripe\Product::search([
            'query' => "metadata['package_id']:'{$package->id}'",
        ])->data[0] ?? null;

        if ($existing) {
            return $existing;
        }

        return \Stripe\Product::create([
            'name' => $package->name,
            'description' => $package->description,
            'metadata' => [
                'package_id' => $package->id,
            ],
        ]);
    }

    /**
     * Otkazuje Stripe pretplatu
     *
     * @param string $subscriptionId
     * @param bool $cancelImmediately - Da li odmah otkazati ili na kraju perioda
     * @return \Stripe\Subscription
     */
    public function cancelSubscription(string $subscriptionId, bool $cancelImmediately = true)
    {
        try {
            if ($cancelImmediately) {
                // Potpuno brisanje pretplate (odmah)
                return $this->stripe->subscriptions->cancel($subscriptionId);
            } else {
                // Otkazivanje na kraju perioda
                return $this->stripe->subscriptions->update(
                    $subscriptionId,
                    ['cancel_at_period_end' => true]
                );
            }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            \Log::error('Stripe cancel error: '.$e->getMessage());
            throw new \Exception("Failed to cancel subscription: ".$e->getMessage());
        }
    }

    /**
     * Vraća detalje o pretplati
     */
    public function getSubscription(string $subscriptionId)
    {
        return $this->stripe->subscriptions->retrieve($subscriptionId);
    }

    /**
     * Reaktivira otkazanu pretplatu
     */
    public function reactivateSubscription(string $subscriptionId)
    {
        return $this->stripe->subscriptions->update(
            $subscriptionId,
            ['cancel_at_period_end' => false]
        );
    }
}
