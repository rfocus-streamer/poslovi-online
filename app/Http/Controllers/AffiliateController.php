<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AffiliateController extends Controller
{

    public function index()
    {
        return view('affiliate.index');
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
            'amount' => ['required', 'numeric', 'min:10', 'max:' . auth()->user()->affiliate_balance],
            'payment_method' => ['required', 'in:paypal,bank,crypto']
        ]);

        try {
            // DB::transaction(function() use ($request) {
            //     $user = auth()->user();
            //     $amount = $request->amount;

            //     // Kreiranje zahteva za isplatu
            //     $payout = AffiliatePayout::create([
            //         'user_id' => $user->id,
            //         'amount' => $amount,
            //         'payment_method' => $request->payment_method,
            //         'paypal_email' => $request->paypal_email,
            //         'bank_account' => $request->bank_account,
            //         'status' => 'pending'
            //     ]);

            //     // Smanjite affiliate balance
            //     $user->decrement('affiliate_balance', $amount);

            //     // Kreirajte transakciju
            //     AffiliateTransaction::create([
            //         'user_id' => $user->id,
            //         'amount' => -$amount,
            //         'type' => 'payout',
            //         'description' => 'Zahtev za isplatu #' . $payout->id
            //     ]);

            //     // Pošaljite notifikaciju adminu
            //     // Notification::send($admins, new NewPayoutRequest($payout));
            // });

            $successMessage = 'Uspešno ste poslali zahtev za isplatu';
            // Postavljanje flash poruke u sesiju
            $request->session()->flash('success', $successMessage);

            return response()->json(['success' => true, 'message' => 'Uspešno ste poslali zahtev za isplatu']);
        } catch (\Exception $e) {
            Log::error('Payout error: ' . $e->getMessage());
            $errorMessage = 'Došlo je do greške prilikom obrade zahteva';
            // Postavljanje flash poruke u sesiju
            $request->session()->flash('error', $errorMessage);
            return response()->json(['success' => false, 'message' => 'Došlo je do greške prilikom obrade zahteva'], 500);
        }
    }
}
