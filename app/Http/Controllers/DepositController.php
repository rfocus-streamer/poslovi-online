<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use App\Services\PayPalService;


class DepositController extends Controller
{
    protected $payPalService;

    public function __construct(PayPalService $payPalService)
    {
        $this->payPalService = $payPalService;
    }

    public function showDepositForm()
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get();
        $user = Auth::user();
        $reserved_amount = Project::where('buyer_id', Auth::id())->sum('reserved_funds');

        return view('payments.deposit', compact(
            'categories',
            'reserved_amount'
        ));
    }

    public function createPayPalPayment(Request $request)
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
                return $this->handleStripePayment($transaction);

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
}
