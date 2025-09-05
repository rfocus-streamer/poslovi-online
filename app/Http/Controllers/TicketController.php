<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketResponse;
use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (in_array($user->role, ['support', 'admin'])) {
            // Ako je support ili admin — prikaz svih tiketa sa statusom 'open' ili 'in_progress'
            $tickets = \App\Models\Ticket::whereIn('status', ['open', 'in_progress'])->where('assigned_team', $user->role)->latest()->paginate(10);
        } else {
            // Običan korisnik vidi samo svoje tikete
            $tickets = $user->tickets()->latest()->paginate(10);
        }
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
            //'attachment' => 'nullable|file|max:2048', // Max 2MB
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

        if (auth()->user()->role === 'admin') {
            $ticket->assigned_team = 'support';
            $ticket->save();
        }

        $this->sendEmail($ticket);

        return back()->with('success', 'Odgovor uspešno dodat!');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate(['status' => 'required|in:open,closed']);

        $ticket->update(['status' => $request->status]);

        return back()->with('success', 'Status uspešno ažuriran!');
    }

    public function markAsRead(TicketResponse $response)
    {
        $ticketResponse = TicketResponse::were('id', $response->id)->first();

        if (auth()->id() !== $response->user_id && $ticketResponse) {
            $ticketResponse->read_at = now();
            $ticketResponse->save();
            return response()->json([
                'success' => true,
                'unread_count' => auth()->user()->unreadTicketResponsesCount()
            ]);
        }

        return response()->json(['success' => false]);
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
            'from_email' => config('mail.from.address'),
            'from' => 'Poslovi Online Podrška',
            'ticket_id' => $ticket->id
        ];

        Mail::to($user->email)->send(new ContactMail($details));

        return back()->with('success', 'Email je uspešno poslat!');
    }
}
