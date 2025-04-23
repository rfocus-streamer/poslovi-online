<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use App\Models\Project;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\CartItem;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Carbon\Carbon;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        // Dekriptovanje service_id i user_id
        try {
            $user_id = Crypt::decrypt($request->input('user_id', ''));
        } catch (\Exception $e) {
            $user_id = Auth::id();
        }

        $directChatService = null;

        $favoriteCount = 0;
        $cartCount = 0;
        $projectCount = 0;
        $chats = [];
        $categories = Category::with('subcategories')->whereNull('parent_id')->get(); // Dohvati sve

        // Poruke između ulogovanog korisnika i nekog drugog korisnika
        $messages = Message::where(function($query){
            $query->where('sender_id', Auth::id())
                  ->orWhere('receiver_id', Auth::id());
        })
        ->with([
                'sender',
                'receiver',
                'service' => fn($q) => $q->select('id', 'title') // Dodajte servis
            ])
        ->get();

        // Prikupite sve jedinstvene kontakte
        $contacts = collect();

        foreach ($messages as $message) {
            // Ako poruka nije od trenutnog korisnika
            if ($message->sender_id != Auth::id()) {
                // Pronađi kontakt (sender)
                $contact = $message->sender;

                // Ako kontakt već postoji u kolekciji, dodaj novu temu i ažuriraj poslednju poruku
                if ($contacts->contains('id', $contact->id)) {
                    $existingContact = $contacts->firstWhere('id', $contact->id);

                    // Dodaj novu temu sa ID servisa i datumom poslednje poruke
                    $existingContact->service_titles->push([
                        'service_id' => $message->service->id,
                        'service_title' => $message->service->title ?? 'Nepoznat servis',
                        'last_message_date' => $message->created_at,
                    ]);
                } else {
                    // Ako kontakt nije u kolekciji, dodaj ga sa temama, servisima i poslednjom porukom
                    $contact->service_titles = collect([[
                        'service_id' => $message->service->id,
                        'service_title' => $message->service->title ?? 'Nepoznat servis',
                        'last_message_date' => $message->created_at,
                    ]]);

                    $contacts->push($contact);
                }
            }

            if ($message->receiver_id != Auth::id()) {
                // Pronađi kontakt (sender)
                $contact = $message->receiver;

                // Ako kontakt već postoji u kolekciji, dodaj novu temu i ažuriraj poslednju poruku
                if ($contacts->contains('id', $contact->id)) {
                    $existingContact = $contacts->firstWhere('id', $contact->id);

                    // Dodaj novu temu sa ID servisa i datumom poslednje poruke
                    $existingContact->service_titles->push([
                        'service_id' => $message->service->id,
                        'service_title' => $message->service->title ?? 'Nepoznat servis',
                        'last_message_date' => $message->created_at,
                    ]);
                } else {
                    // Ako kontakt nije u kolekciji, dodaj ga sa temama, servisima i poslednjom porukom
                    $contact->service_titles = collect([[
                        'service_id' => $message->service->id,
                        'service_title' => $message->service->title ?? 'Nepoznat servis',
                        'last_message_date' => $message->created_at,
                    ]]);

                    $contacts->push($contact);
                }
            }
        }


        if($request->has('service_id'))
        {
            $service_id = Crypt::decrypt($request->input('service_id', ''));

            $service = Service::find($service_id);
            if($service_id){
                $directChatService = $service;
                // Dohvatanje korisnika koji je prodavac
                $seller = (object) $service->user->only([
                    'id',
                    'user_id',
                    'firstname',
                    'lastname',
                    'avatar',
                    'stars',
                    'is_online',
                    'role',
                    'last_seen_at'
                ]);

                // Inicijalizacija 'service_titles' kao prazne kolekcije ili niza
                $seller->service_titles = collect(); // Možete koristiti i običan niz umesto kolekcije

                // Ako kontakt već postoji u kolekciji, dodaj novu temu i ažuriraj poslednju poruku
                if ($contacts->contains('id', $seller->id)) {
                    $existingContact = $contacts->firstWhere('id', $seller->id);

                    // Ako već postoji, dodaj novu temu sa ID servisa i datumom poslednje poruke
                    $existingContact->service_titles = $existingContact->service_titles ?? collect();

                    // Dodaj novu temu sa ID servisa i datumom poslednje poruke
                    $existingContact->service_titles->push([
                        'sender_id' => $user_id,
                        'service_id' => $service->id,
                        'service_title' => $service->title ?? 'Nepoznat servis',
                        'last_message_date' => null,
                    ]);
                } else {
                    // Ako kontakt nije u kolekciji, dodaj ga sa temama, servisima i poslednjom porukom
                    $seller->service_titles->push([
                        'sender_id' => $user_id,
                        'service_id' => $service->id,
                        'service_title' => $service->title ?? 'Nepoznat servis',
                        'last_message_date' => null,
                    ]);

                    $contacts->push($seller);
                }
            }
        }

        // Na kraju, uklanjamo duplikate tema i servis ID-ova i ažuriramo poslednje poruke
        $contacts->transform(function($contact) {
            // Uklanjanje duplikata servisa unutar service_titles
            $contact->service_titles = $contact->service_titles->unique(function ($item) {
                return $item['service_id']; // Unique po ID servisa
            })->values();

            // Formatiranje datuma poslednje poruke sa vremenom
            $contact->service_titles->transform(function($service) {
                if($service['last_message_date'] !== null){
                    $service['last_message_date'] = \Carbon\Carbon::parse($service['last_message_date'])
                        ->setTimezone('Europe/Belgrade')  // Postavi vremensku zonu na Belgrade
                        ->format('M d, Y h:i:s');          // Formatiraj datum
                }
                return $service;
            });

            return $contact;
        });

        // Originalni kod sa jedinstvenošću po user_id + service_id
        // $contacts = $contacts->unique(function ($item) {
        //     if (property_exists($item, 'sender_id')) {
        //         return $item['sender_id'] . '-' . $item['service_id'];
        //     }
        // });

        if (Auth::check()) { // Provera da li je korisnik ulogovan
            $favoriteCount = Favorite::where('user_id', Auth::id())->count();
            $cartCount = CartItem::where('user_id', Auth::id())->count();
            $projectCount = Project::where('buyer_id', Auth::id())->count();
        }

        return view('messages.index', compact('categories', 'contacts', 'favoriteCount', 'cartCount', 'chats', 'messages', 'user_id', 'directChatService'));
    }

    public function send(Request $request)
    {
        try {
            //\Log::info($request->all());

            // Валидација са стварним ID-евима
            $validated = $request->validate([
                'service_id' => 'required|exists:services,id', // Сада ради проверу у бази
                'user_id' => 'required|exists:users,id',
                'content' => 'required|string',
                'attachment' => 'nullable|file|max:50240'
            ]);

            $user = Auth::user();

            $unReadMessages = [];

            if ($request->has('unreadMessages') && !empty($request->input('unreadMessages'))) {
                $unReadMessages = json_decode($request->input('unreadMessages'), true);
            }

            // Ako postoje nepročitane poruke, ažuriraj ih u bazi
            if (!empty($unReadMessages)) {
                foreach ($unReadMessages as $messageData) {
                    // Provera da li su podaci u pravilnom formatu
                    if (isset($messageData['message_id']) && isset($messageData['read_at'])) {
                        // Pronađi poruku po ID-u
                        $message = Message::find($messageData['message_id']);
                        if ($message) {
                            // Formatiraj read_at u ispravan timestamp format
                            $formattedReadAt = Carbon::createFromFormat('d.m.Y. H:i:s', $messageData['read_at'])->format('Y-m-d H:i:s');

                            // Ažuriraj datum kada je poruka pročitana
                            $message->read_at = $formattedReadAt;
                            $message->save();
                        }
                    }
                }
            }

            $message = Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $request->input('user_id'),
                'content' => $request->content,
                'service_id' => $request->input('service_id'),
                'attachment_path' => $request->file('attachment') ? $request->file('attachment')->store('attachments') : null
            ]);

            $message->load('sender'); // Učitaj vezani model

            broadcast(new MessageSent($message))->toOthers();

            return response()->json([
                'success' => true,
                'unReadMessages' => $unReadMessages,
                'message' => $message->only(['id', 'sender_id', 'receiver_id', 'read_at'])
            ]);

        } catch (\Throwable $e) {
            \Log::error($e);
            return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
        }
    }

    public function markAsRead(Request $request, $id)
    {
        // Pronađi poruku pre nego što je ažuriraš
        $message = Message::where('id', $id)
               ->where('receiver_id', auth()->id())
               ->first(); // Dohvati ceo objekat

        if (!$message) {
            return response()->json(['error' => 'Poruka nije pronađena:'.auth()->id()], 404);
        }

        // Ažuriraj i osveži objekat
        $message->read_at = \Carbon\Carbon::now('Europe/Belgrade'); // Postavljanje Beogradskog vremena
        $message->save(); // save() će automatski osvežiti objekat

        $unreadCounts = [
            'total' => Message::where('receiver_id', auth()->id())
                ->whereNull('read_at')
                ->count(),
            'per_service' => Message::where('receiver_id', auth()->id())
                ->whereNull('read_at')
                ->groupBy('service_id')
                ->selectRaw('service_id, count(*) as count')
                ->pluck('count', 'service_id')
        ];

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'unread_counts' => $unreadCounts,
            'message' => $message->only(['id', 'sender_id', 'receiver_id', 'read_at'])
        ]);
    }

    public function getMessages(Request $request)
    {
        $serviceId = $request->input('service_id');
        $contactId = $request->input('contact_id');
        // Proverite da li je korisnik autorizovan da vidi ove poruke
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Dobijte sveže poruke iz baze
        $messages = Message::where(function($query) use ($contactId) {
                $query->where('sender_id', auth()->id())
                      ->where('receiver_id', $contactId);
            })
            ->orWhere(function($query) use ($contactId) {
                $query->where('sender_id', $contactId)
                      ->where('receiver_id', auth()->id());
            })
            ->where('service_id', $serviceId)
            ->with([
                'sender' => function($query) {
                    $query->select([
                        'id',
                        'firstname',
                        'lastname',
                        'avatar',
                        'stars',
                        'is_online',
                        'role',
                        'last_seen_at'
                    ]);
                },
                'receiver' => function($query) {
                    $query->select([
                        'id',
                        'firstname',
                        'lastname',
                        'avatar',
                        'stars',
                        'is_online',
                        'role',
                        'last_seen_at'
                    ]);
                }
            ])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages
        ]);
    }
}
