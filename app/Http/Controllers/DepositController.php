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
        $categories = Category::with('subcategories')->whereNull('parent_id')->get(); // Dohvati sve
        $user = Auth::user();
        $reserved_amount = Project::where('buyer_id', Auth::id())->sum('reserved_funds');
        $projects = [];
        $favoriteCount = 0;
        $cartCount = 0;
        $projectCount = 0;
        return view('payments.deposit', compact('categories', 'favoriteCount', 'cartCount', 'projectCount', 'reserved_amount'));
    }

    public function createPayPalPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:5',
            'currency' => 'required|in:USD,EUR',
            'payment_method' => 'required|in:paypal,stripe,wise'
        ]);

        $amount = (float) $request->input('amount'); // Osigurajte da je $amount broj
        $currency = $request->input('currency');//'USD'; // Valuta mora biti string
        $description = 'Deposit to account'; // Opis mora biti string
        $successUrl = route('deposit.paypal.success'); // URL za uspeh
        $cancelUrl = route('deposit.paypal.cancel'); // URL za otkazivanje

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
                    $payment = $this->payPalService->createPayment($amount, $currency, $description, $successUrl, $cancelUrl);
                        return redirect($payment->getApprovalLink());
                    } catch (\Exception $e) {
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
        $paymentId = $request->input('paymentId');
        $payerId = $request->input('PayerID');

        if (!$paymentId || !$payerId) {
            return redirect()->route('deposit.index')->with('error', 'Payment ID or Payer ID missing.');
        }

        try {
            $payment = $this->payPalService->executePayment($paymentId, $payerId);

            // Ovde možete dodati logiku za čuvanje depozita u bazu
            $deposit = new Deposit();
            $deposit->user_id = Auth::id();
            $deposit->amount = $payment->transactions[0]->amount->total;
            $deposit->currency = $payment->transactions[0]->amount->currency;
            $deposit->status = 'completed';
            $deposit->save();

            return redirect()->route('deposit.form')->with('success', 'Deposit successful!');
        } catch (\Exception $e) {
            return redirect()->route('deposit.form')->with('error', $e->getMessage());
        }
    }

    public function payPalCancel()
    {
        return redirect()->route('deposit.form')->with('error', 'Payment cancelled.');
    }
}
