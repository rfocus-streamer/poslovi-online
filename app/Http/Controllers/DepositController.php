<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deposit;
use Illuminate\Support\Facades\Auth;

class DepositController extends Controller
{
    public function create() {
        return view('deposit.create');
    }

    public function store(Request $request) {
        $request->validate([
            'amount' => 'required|numeric|min:10', // Minimalan depozit 10 RSD
            'payment_method' => 'required|in:paypal,credit_card,bank_transfer',
        ]);

        $deposit = Deposit::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'status' => 'pending', // Po defaultu transakcija čeka odobrenje
        ]);

        return redirect()->route('deposit.create')->with('success', 'Uspešno ste poslali zahtev za depozit!');
    }
}
