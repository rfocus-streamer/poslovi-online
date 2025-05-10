<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = auth()->user()->tickets()->latest()->paginate(10);  // Paginacija sa 10 stavki po stranici;
        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'attachment' => 'nullable|file|max:2048', // Max 2MB
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        Ticket::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'attachment' => $attachmentPath,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('tickets.index')->with('success', 'Ticket uspešno kreiran!');
    }

    public function show(Ticket $ticket)
    {
        // Eager load odgovore sa korisnicima i user relaciju
        $ticket->load(['responses.user', 'user']);

        return view('tickets.show', compact('ticket'));
    }

    public function redirectToTeam(Request $request, Ticket $ticket)
    {
        $request->validate(['team' => 'required|in:support,admin']);

        $ticket->update(['assigned_team' => $request->team]);

        return back()->with('success', 'Ticket preusmeren na '.$request->team.' tim!');
    }

    public function storeResponse(Request $request, Ticket $ticket)
    {
        $request->validate([
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:2048'
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('response-attachments', 'public');
        }

        $ticket->responses()->create([
            'content' => $request->content,
            'attachment' => $attachmentPath,
            'user_id' => auth()->id()
        ]);

        $this->sendEmail($ticket);

        return back()->with('success', 'Odgovor uspešno dodat!');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate(['status' => 'required|in:open,closed']);

        $ticket->update(['status' => $request->status]);

        return back()->with('success', 'Status uspešno ažuriran!');
    }

    public function sendEmail($ticket)
    {
        // Dobijamo korisnika povezanog sa tiketom
        $user = $ticket->user;

        // Proverimo ako korisnik ne postoji
        if (!$user || $user->email == auth()->user()->email) {
            return;
        }

        // Pravimo asocijativni niz sa samo potrebnim podacima
        $details = [
            'first_name' => $user->firstname,
            'last_name' => $user->lastname,
            'email' => $user->email,
            'message' => 'Telo poruke iz kontrolera', // Možete dodati dinamicki tekst
            'template' => 'emails.tickets', // Predloženi Blade šablon,
            'subject' => 'Obaveštenje o odgovoru na tiket: '.$ticket->title,
            'from_email' => 'gligorijesaric@gmail.com',
            'from' => 'Poslovi Online Podrška',
            'ticket_id' => $ticket->id
        ];

        Mail::to($user->email)->send(new ContactMail($details));

        return back()->with('success', 'Email je uspešno poslat!');
    }
}
