<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use App\Services\PayPalService;
use App\Services\StripeService;

class DepositController extends Controller
{
    protected $payPalService;
    protected $stripeService;

    public function __construct(PayPalService $payPalService, StripeService $stripeService)
    {
        $this->payPalService = $payPalService;
        $this->stripeService = $stripeService;
    }

    public function showDepositForm()
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get();
        $user = Auth::user();
        $reserved_amount = Project::where('buyer_id', Auth::id())->sum('reserved_funds');
        $stripeKey=  config('services.stripe.public');
        $totalEarnings = 0;

        return view('payments.deposit', compact(
            'categories',
            'reserved_amount',
            'stripeKey',
            'totalEarnings'
        ));
    }

    public function createPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:5',
            'currency' => 'required|in:USD,EUR',
            'payment_method' => 'required|in:paypal,stripe,wise'
        ]);

        $amount = (float)$request->input('amount');
        $currency = $request->input('currency');
        $description = 'Deposit to account';
        $successUrl = route('deposit.paypal.success');
        $cancelUrl = route('deposit.paypal.cancel');

        // Kreiraj transakciju
        $transaction = Transaction::create([
            'user_id' => auth()->id(),
            'amount' => $amount,
            'currency' => $currency,
            'payment_method' => $request->payment_method,
            'status' => 'pending'
        ]);

        switch ($request->payment_method) {
            case 'paypal':
                try {
                    $paymentOrder = $this->payPalService->createPayment(
                        $amount,
                        $currency,
                        $description,
                        $successUrl,
                        $cancelUrl
                    );

                    // Ažuriraj transakciju sa PayPal order ID
                    $transaction->update([
                        'transaction_id' => $paymentOrder->id
                    ]);

                    // Pronađi approval link
                    $approveUrl = collect($paymentOrder->links)
                        ->firstWhere('rel', 'approve')->href;

                    return redirect()->away($approveUrl);

                } catch (\Exception $e) {
                    $transaction->update(['status' => 'failed']);
                    return redirect()->back()->with('error', $e->getMessage());
                }

            case 'stripe':
                return $this->handleStripePayment($request, $transaction);

            case 'wise':
                return $this->handleWisePayment($transaction);
        }
    }

    public function payPalSuccess(Request $request)
    {
        $orderId = $request->input('token');

        if (!$orderId) {
            return redirect()->route('deposit.index')->with('error', 'Invalid payment confirmation');
        }

        try {
            // Dobavi transakciju po PayPal order ID
            $transaction = Transaction::where('transaction_id', $orderId)
                ->where('status', 'pending')
                ->firstOrFail();

            // Izvrši plaćanje
            $result = $this->payPalService->executePayment($orderId);

            // Proveri status
            if ($result->status !== 'COMPLETED') {
                throw new \Exception('Payment not completed');
            }

            // Kreiraj depozit
            $deposit = Deposit::create([
                'user_id' => Auth::id(),
                'amount' => $result->purchase_units[0]->amount->value,
                'currency' => $result->purchase_units[0]->amount->currency_code,
                'status' => 'completed'
            ]);

            // Ažuriraj transakciju
            $transaction->update([
                'status' => 'completed',
                'deposit_id' => $deposit->id
            ]);

            return redirect()->route('deposit.form')->with('success', 'Deposit successful!');

        } catch (\Exception $e) {
            return redirect()->route('deposit.form')
                ->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    public function payPalCancel()
    {
        return redirect()->route('deposit.form')->with('error', 'Payment cancelled.');
    }

    // Stripe
    public function handleStripePayment(Request $request, $transaction)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method_stripe' => 'required|string',
        ]);

        try {
            $returnUrl = route('deposit.form');
            $paymentIntent = $this->stripeService->createPayment(
                $request->amount,
                $request->currency,
                $request->payment_method_stripe,
                $returnUrl
            );

            // Ažuriraj transakciju sa PayPal order ID
            $transaction->update([
                'transaction_id' => $paymentIntent->id
            ]);


            if ($paymentIntent->status === 'requires_action') {
                return redirect()->away($paymentIntent->next_action->redirect_to_url->url);
            }

            if ($paymentIntent->status === 'succeeded') {
                return $this->handleSuccessfulPayment($transaction, $paymentIntent);
            }

            throw new \Exception('Unexpected payment status: ' . $paymentIntent->status);

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Obrada povratnog URL-a nakon Stripe plaćanja (za 3D Secure i sl.)
     */
    public function handlePaymentReturn(Request $request)
    {
        try {
            // Proverite da li postoji payment_intent parametar
            if (!$request->has('payment_intent')) {
                throw new \Exception('Nedostaju parametri za potvrdu plaćanja');
            }

            $paymentIntentId = $request->input('payment_intent');

            // Pronađite transakciju po payment_intent ID-u
            $transaction = Transaction::where('transaction_id', $paymentIntentId)
                            ->where('status', 'pending')
                            ->firstOrFail();

            // Proverite status plaćanja sa Stripe-a
            $paymentIntent = $this->stripeService->retrievePaymentIntent($paymentIntentId);

            // Obrada različitih statusa
            switch ($paymentIntent->status) {
                case 'succeeded':
                    return $this->handleSuccessfulPayment($transaction, $paymentIntent);

                case 'processing':
                    return redirect()->route('deposit.form')
                           ->with('info', 'Plaćanje se još uvek obrađuje. Obavestićemo vas kada bude završeno.');

                case 'requires_payment_method':
                    $transaction->update(['status' => 'failed']);
                    return redirect()->route('deposit.form')
                           ->with('error', 'Plaćanje nije uspelo. Molimo pokušajte ponovo sa drugom karticom.');

                default:
                    throw new \Exception('Neočekivan status plaćanja: ' . $paymentIntent->status);
            }

        } catch (\Exception $e) {
            Log::error('Stripe return error: ' . $e->getMessage());
            return redirect()->route('deposit.form')
                   ->with('error', 'Došlo je do greške pri obradi plaćanja: ' . $e->getMessage());
        }
    }

    /**
     * Pomocna metoda za uspešno plaćanje
     */
    private function handleSuccessfulPayment($transaction, $paymentIntent)
    {
        // Dohvatanje trenutno prijavljenog korisnika
        $user = auth()->user();

        // Dodavanje iznosa na postojeći balans
        $user->deposits += $transaction->amount;
        $user->save();

        // Ažurirajte transakciju
        $transaction->update([
            'status' => 'completed',
            'metadata' => json_encode([
                'stripe_payment_intent' => $paymentIntent->id,
                'payment_method' => $paymentIntent->payment_method
            ])
        ]);

        return redirect()->route('deposit.form')
               ->with('success', 'Plaćanje je uspešno izvršeno! Iznos od ' .
                      $transaction->amount . ' ' . $transaction->currency .
                      ' je dodat na vaš nalog.');
    }
}
