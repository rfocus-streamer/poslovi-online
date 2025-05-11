<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AffiliatePayout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AffiliateController extends Controller
{

    public function index(Request $request)
    {
        $payouts = auth()->user()->affiliatePayouts()
                    ->orderBy('request_date', 'desc')
                    ->paginate(10); // 10 itema po strani

        return view('affiliate.index', compact('payouts'));
    }

    public function activateAffiliate(Request $request)
    {
        $user = Auth::user();
        $user->affiliate_accepted = true;
        $user->save();
         return redirect()->back()->with('success', 'Uspešno si aktivirao affiliate program !');
    }

    public function requestPayout(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:100', 'max:' . auth()->user()->affiliate_balance],
            'payment_method' => ['required', 'in:paypal,credit_card,bank_account'],
            'paypal_email' => ['required_if:payment_method,paypal', 'email', 'nullable'],
            'bank_account' => ['required_if:payment_method,bank_account', 'string', 'nullable'],
            'credit_card' => ['required_if:payment_method,credit_card', 'string', 'nullable']
        ], [
            'paypal_email.required_if' => 'PayPal email je obavezan kada se odabere PayPal kao način plaćanja.',
            'bank_account.required_if' => 'Broj bankovnog računa je obavezan kada se odabere bankovni transfer.',
            'credit_card.required_if' => 'Podaci kreditne kartice su obavezni kada se odabere plaćanje karticom.'
        ]);

        try {
            DB::transaction(function() use ($request) {
                $user = auth()->user();
                $amount = $request->amount;

                // Kreiranje zahteva za isplatu
                $payout = AffiliatePayout::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'payment_method' => $request->payment_method,
                    'payment_details' => $request->payment_method === 'paypal'
                        ? $request->paypal_email
                        : ($request->payment_method === 'bank_account' ? $request->bank_account : null),
                    'request_date' => now(),
                    'affiliate_balance' => $user->affiliate_balance,
                    'status' => 'requested'
                ]);

                // Smanjite affiliate balance
                $user->decrement('affiliate_balance', $amount);
            });

            return response()->json([
                'success' => true,
                'message' => 'Uspešno je poslat zahtev za isplatu',
                'new_balance' => auth()->user()->fresh()->affiliate_balance
            ]);

        } catch (\Exception $e) {
            Log::error('Payout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Došlo je do greške prilikom obrade zahteva: ' . $e->getMessage()
            ], 500);
        }
    }
}
