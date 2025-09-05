<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{

    public function index()
    {
        $invoices = []; // Inicijalizuj prazan niz za omiljene servise

        if (Auth::check()) { // Proverite da li je korisnik ulogovan
            // Dohvati za trenutno ulogovanog korisnika sa paginacijom
            $invoices = Invoice::where('user_id', Auth::id())
                                ->orderBy('created_at', 'desc') // Sortiraj od najnovijih ka najstarijima
                                ->paginate(50); // Paginacija sa 50 stavke po stranici
        }

        return view('invoices.index', compact('invoices'));
    }

    public function getPDF($id)
    {
        $invoice = Invoice::findOrFail($id);
        $user = auth()->user();

        // Ako korisnik NIJE admin i NIJE vlasnik invoice-a → zabranjen pristup
        if ($user->role !== 'admin' && $invoice->user_id !== $user->id) {
            abort(403, 'Access denied');
        }

        $pdf = PDF::loadView('invoices.template', compact('invoice'));

        return $pdf->stream("invoice-{$invoice->number}.pdf");
    }
}
