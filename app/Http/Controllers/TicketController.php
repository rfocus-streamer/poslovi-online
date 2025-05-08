<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
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
        return view('tickets.show', compact('ticket'));
    }

    public function index()
    {
        $tickets = auth()->user()->tickets()->latest()->get();
        return view('tickets.index', compact('tickets'));
    }
}
