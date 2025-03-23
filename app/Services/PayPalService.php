<?php
namespace App\Services;

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\PaymentExecution;

class PayPalService
{
    protected $apiContext;

    public function __construct()
    {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                config('services.paypal.client_id'),
                config('services.paypal.secret')
            )
        );

        $this->apiContext->setConfig(config('services.paypal.settings'));
        $this->apiContext->setConfig([
            'http.CURLOPT_SSL_VERIFYPEER' => false,
            'http.CURLOPT_SSL_VERIFYHOST' => false,
        ]);
    }

    public function createPayment($amount, $currency, $description, $successUrl, $cancelUrl)
    {
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $amountObj = new Amount();
        $amountObj->setTotal(number_format($amount, 2, '.', '')); // $amount mora biti broj (npr. 100.00)
        $amountObj->setCurrency($currency); // $currency mora biti string (npr. 'RSD')

        $transaction = new Transaction();
        $transaction->setAmount($amountObj);
        $transaction->setDescription($description); // $description mora biti string

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($successUrl)
            ->setCancelUrl($cancelUrl);

        $payment = new Payment();
        $payment->setIntent('SALE') // Možete probati i sa 'CAPTURE' umesto 'sale'
            ->setPayer($payer)
            ->setTransactions([$transaction]) // Prosleđujete niz sa jednom transakcijom
            ->setRedirectUrls($redirectUrls);

        try {
            $payment->create($this->apiContext);
            return $payment;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function executePayment($paymentId, $payerId)
    {
        try {
            $payment = Payment::get($paymentId, $this->apiContext);

            $execution = new PaymentExecution();
            $execution->setPayerId($payerId);

            $result = $payment->execute($execution, $this->apiContext);
            return $result;
        } catch (\Exception $e) {
            // Logujte grešku za više detalja
            \Log::error('PayPal API Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
