<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FiatPayout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FiatPayoutController extends Controller
{
    public function requestPayout(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'amount' => ['required', 'numeric', 'min:10', 'max:' . auth()->user()->deposits],
            'payment_method' => ['required', 'in:paypal,card,bank'],
            'paypal_email' => ['required_if:payment_method,paypal', 'email', 'nullable'],
            'bank_account' => ['required_if:payment_method,bank', 'string', 'nullable'],
            //'card_number' => ['required_if:payment_method,card', 'string', 'nullable'],
            //'card_holder_name' => ['required_if:payment_method,card', 'string', 'nullable'],
            //'card_expiry_date' => ['required_if:payment_method,card', 'string', 'nullable']
        ]);

        try {
            DB::transaction(function() use ($request) {
                $user = auth()->user();
                $amount = $request->amount;

                // Logovanje podataka pre nego što ih sačuvaš u bazi
                \Log::info('Zahtev za isplatu:', $request->all());

                // Kreiranje zahteva za isplatu
                $payout = FiatPayout::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'payment_method' => $request->payment_method,
                     'payment_details' => $request->payment_method === 'paypal'
                        ? $request->paypal_email
                        : ($request->payment_method === 'bank'
                            ? $request->bank_account
                            : ($request->payment_method === 'card'
                                ? 'Card'
                                : '')),

                    'card_number' => $request->payment_method === 'card' ? $request->card_number : null,
                    'card_holder_name' => $request->payment_method === 'card' ? $request->card_holder : null,
                    'card_expiry_date' => $request->payment_method === 'card' ? $request->card_expiry : null,
                    'request_date' => now(),
                    'deposits' => $user->deposits,
                    'status' => 'requested'
                ]);

                // Smanji balance
                $user->decrement('deposits', $amount);
            });

            return response()->json([
                'success' => true,
                'message' => 'Uspešno je poslat zahtev za isplatu',
                'new_balance' => auth()->user()->fresh()->deposits
            ]);
        } catch (\Exception $e) {
            Log::error('Payout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Došlo je do greške prilikom obrade zahteva: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $payout = FiatPayout::with('user')->findOrFail($id);
        return view('admin.partials.payout_details', compact('payout'));
    }

    public function approve($id)
    {
        $payout = FiatPayout::findOrFail($id);

        $transactionId = request('transaction_id');
        if (!$transactionId) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction ID je obavezan'
            ]);
        }

        $payout->update([
            'status' => 'completed',
            'payed_date' => now(),
            'transaction_id' => $transactionId// id od paypal ili stripe
        ]);

        return response()->json(['success' => true]);
    }

    public function reject($id)
    {
        $payout = FiatPayout::findOrFail($id);
        $payout->update(['status' => 'rejected']);
        return response()->json(['success' => true]);
    }
}
