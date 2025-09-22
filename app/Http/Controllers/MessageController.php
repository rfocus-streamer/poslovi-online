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
use App\Models\BlockedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;  // Uvozimo DB fasadu
use Illuminate\Contracts\Encryption\DecryptException;
use Carbon\Carbon;

class MessageController extends Controller
{

    public function index(Request $request)
    {
        // Dekriptovanje service_id i user_id
        try {
            $user_id = Crypt::decryptString($request->query('buyer_id'));
        } catch (\Exception $e) {
            $user_id = Auth::id();
        }

        $user = Auth::user();
        $token = $user->createToken('posloviOnline')->plainTextToken;

        $directChatService = null;
        $chats = [];

        // Poruke između ulogovanog korisnika i nekog drugog korisnika
        $messages = Message::where(function($query){
            $query->where('sender_id', Auth::id())
                  ->orWhere('receiver_id', Auth::id());
        })
        ->with([
                'sender',
                'receiver',
                'service' => fn($q) => $q->select('id', 'title')
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

                    $unreadCount = Message::countUnreadForSender(Auth::id(), $contact->id, $message->service->id);

                    // Dodaj novu temu sa ID servisa i datumom poslednje poruke
                    $existingContact->service_titles->push([
                        'service_id' => $message->service->id,
                        'service_title' => $message->service->title ?? 'Nepoznat servis',
                        'last_message_date' => $message->created_at,
                        'unreadCount' => $unreadCount
                    ]);
                } else {
                    $unreadCount = Message::countUnreadForSender(Auth::id(), $contact->id, $message->service->id);
                    // Ako kontakt nije u kolekciji, dodaj ga sa temama, servisima i poslednjom porukom
                    $contact->service_titles = collect([[
                        'service_id' => $message->service->id,
                        'service_title' => $message->service->title ?? 'Nepoznat servis',
                        'last_message_date' => $message->created_at,
                        'unreadCount' => $unreadCount
                    ]]);

                    $contacts->push($contact);
                }
            }

            if ($message->receiver_id != Auth::id()) {
                // Pronađi kontakt (receiver)
                $contact = $message->receiver;
                $unreadCount = Message::countUnreadForSender(Auth::id(), $contact->id, $message->service->id);

                // Ako kontakt već postoji u kolekciji, dodaj novu temu i ažuriraj poslednju poruku
                if ($contacts->contains('id', $contact->id)) {
                    $existingContact = $contacts->firstWhere('id', $contact->id);

                    // Dodaj novu temu sa ID servisa i datumom poslednje poruke
                    $existingContact->service_titles->push([
                        'service_id' => $message->service->id,
                        'service_title' => $message->service->title ?? 'Nepoznat servis',
                        'last_message_date' => $message->created_at,
                        'unreadCount' => $unreadCount
                    ]);
                } else {
                    // Ako kontakt nije u kolekciji, dodaj ga sa temama, servisima i poslednjom porukom
                    $contact->service_titles = collect([[
                        'service_id' => $message->service->id,
                        'service_title' => $message->service->title ?? 'Nepoznat servis',
                        'last_message_date' => $message->created_at,
                        'unreadCount' => $unreadCount
                    ]]);

                    $contacts->push($contact);
                }
            }
        }

        // Direktni chat logika
        if($request->has('service_id') and $request->has('buyer_id')) {
                $buyer_id = Crypt::decryptString($request->query('buyer_id'));
                $service_id = Crypt::decrypt($request->input('service_id', ''));

                $service = Service::select('id', 'title')->find($service_id);
                if($service_id){
                    $directChatService = $service;
                    $directChatService->user_id = $buyer_id;
                    // Dohvatanje korisnika koji je prodavac
                    $buyer = User::where('id', $buyer_id)
                        ->select([
                            'id',
                            'firstname',
                            'lastname',
                            'avatar',
                            'stars',
                            'is_online',
                            'role',
                            'last_seen_at'
                        ])
                        ->first();

                    // Inicijalizacija 'service_titles' kao prazne kolekcije ili niza
                    $buyer->service_titles = collect(); // Možete koristiti i običan niz umesto kolekcije

                    // Ako kontakt već postoji u kolekciji, dodaj novu temu i ažuriraj poslednju poruku
                    if ($contacts->contains('id', $buyer->id)) {
                        $existingContact = $contacts->firstWhere('id', $buyer->id);

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
                        $buyer->service_titles->push([
                            'sender_id' => $user_id,
                            'service_id' => $service->id,
                            'service_title' => $service->title ?? 'Nepoznat servis',
                            'last_message_date' => null,
                        ]);

                        $contacts->push($buyer);
                    }
                }
        }
        elseif($request->has('service_id') and $request->has('seller_id')) {
                $seller_id = Crypt::decryptString($request->query('seller_id'));
                $service_id = Crypt::decrypt($request->input('service_id', ''));

                $service = Service::select('id', 'title')->find($service_id);
                if($service_id){
                    $directChatService = $service;
                    $directChatService->user_id = $seller_id;
                    // Dohvatanje korisnika koji je prodavac
                    $seller = User::where('id', $seller_id)
                        ->select([
                            'id',
                            'firstname',
                            'lastname',
                            'avatar',
                            'stars',
                            'is_online',
                            'role',
                            'last_seen_at'
                        ])
                        ->first();

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
        elseif($request->has('service_id')) {
                $service_id = Crypt::decrypt($request->input('service_id', ''));
                $service = Service::with(['user' => function($query) {
                                $query->select( 'id',
                                                'firstname',
                                                'lastname',
                                                'avatar',
                                                'stars',
                                                'is_online',
                                                'role',
                                                'last_seen_at'
                                ); // Izaberite kolone koje želite od usera
                            }])
                            ->select('id', 'title', 'user_id') // Obavezno uključite user_id
                            ->find($service_id);
                if($service_id){
                    $directChatService = $service;
                    // Dohvatanje korisnika koji je prodavac
                    $seller = (object) $service->user;

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

        // DODATAK: Izračunaj ukupan broj nepročitanih i najnoviji datum nepročitane poruke za svaki kontakt
        $contacts = $contacts->map(function ($contact) {
            // Ukupan broj nepročitanih poruka za kontakt
            $contact->total_unread = $contact->service_titles->sum('unreadCount');

            // Pronađi najnoviji datum nepročitane poruke
            $contact->latest_unread = $contact->service_titles
                ->where('unreadCount', '>', 0)
                ->max('last_message_date');

            return $contact;
        })->sortByDesc(function ($contact) {
            // Prvo sortiraj po prisustvu nepročitanih poruka (sa nepročitanim ide prvo)
            // Zatim sortiraj po datumu nepročitanih poruka (novije ide prvo)
            // Na kraju sortiraj po ukupnom broju nepročitanih
            return [
                $contact->total_unread > 0 ? 1 : 0, // Kontakti sa nepročitanim porukama dobijaju prioritet
                $contact->latest_unread,            // Novije nepročitane poruke imaju veći prioritet
                $contact->total_unread              // Više nepročitanih poruka ima veći prioritet
            ];
        })->values();

        // Na kraju, uklanjamo duplikate tema i servis ID-ova i ažuriramo poslednje poruke
        $contacts->transform(function($contact) {
            $contact->blocked = BlockedUser::where('user_id', auth()->id())
                           ->where('blocked_user_id', $contact->id)
                           ->exists();
            // Uklanjanje duplikata servisa unutar service_titles
            $contact->service_titles = $contact->service_titles->unique(function ($item) {
                return $item['service_id']; // Unique po ID servisa
            })->values();

            // Formatiranje datuma poslednje poruke sa vremenom
            $contact->service_titles->transform(function($service) {
                if($service['last_message_date'] !== null){
                    $service['last_message_date'] = \Carbon\Carbon::parse($service['last_message_date'])
                        ->setTimezone('Europe/Belgrade')
                        ->format('M d, Y h:i:s');
                }
                return $service;
            });

            return $contact;
        });

        $messagesCount = 0;

        if (Auth::check()) {
            $messagesCount = Message::where('receiver_id', Auth::id())
                                ->where('read_at', null)
                                ->count();
        }

        return view('messages.index', compact('contacts', 'chats', 'messages', 'directChatService', 'messagesCount', 'token'));
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

             // Provera blokiranja
            $blockedByYou = BlockedUser::where('user_id', auth()->id())
                           ->where('blocked_user_id', $request->input('user_id'))
                           ->exists();

            $blockedByHim = BlockedUser::where('user_id', $request->input('user_id'))
                           ->where('blocked_user_id', auth()->id())
                           ->exists();

            if ($blockedByYou) {
                // Bar jedan je ispunjen
                return response()->json([
                    'message' => 'Ti si blokirao ovog korisnika i ne možeš slati poruke.',
                ], 403);
            }

            if ($blockedByHim) {
                // Bar jedan je ispunjen
                return response()->json([
                    'message' => 'Ovaj korisnik te blokirao i ne možeš slati poruke.',
                ], 403);
            }

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
                            if($message->sender_id != $user->id){
                                // Formatiraj read_at u ispravan timestamp format
                                $formattedReadAt = Carbon::createFromFormat('d.m.Y. H:i:s', $messageData['read_at'])->format('Y-m-d H:i:s');

                                // Ažuriraj datum kada je poruka pročitana
                                $message->read_at = $formattedReadAt;
                                $message->save();
                            }
                        }
                    }
                }
            }

            $attachmentName = null;

            if ($request->hasFile('attachment')) {
                $attachmentName = $request->file('attachment')->getClientOriginalName();
            }

            $message = Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $request->input('user_id'),
                'content' => $request->content,
                'service_id' => $request->input('service_id'),
                'attachment_path' => $request->file('attachment') ? $request->file('attachment')->store('attachments') : null,
                'attachment_name' => $attachmentName
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

    public function markAsRead(Request $request)
    {
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
                        if($message->sender_id != $user->id){
                            // Formatiraj read_at u ispravan timestamp format
                            $formattedReadAt = Carbon::createFromFormat('d.m.Y. H:i:s', $messageData['read_at'])->format('Y-m-d H:i:s');

                            // Ažuriraj datum kada je poruka pročitana
                            $message->read_at = $formattedReadAt;
                            $message->save();
                        }
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function getMessages(Request $request)
    {
        $serviceId = $request->input('service_id');
        $contactId = $request->input('contact_id');
        $page = $request->input('page', 1);  // Novi parametar za stranicu

        // Provera blokiranja
        $blockedByYou = BlockedUser::where('user_id', auth()->id())
                           ->where('blocked_user_id', $contactId)
                           ->exists();

        $blockedByHim = BlockedUser::where('user_id', $contactId)
                           ->where('blocked_user_id', auth()->id())
                           ->exists();

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
            ->orderBy('created_at', 'desc') // Sortiraj od najnovijih ka najstarijima
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
            ->paginate(10, ['*'], 'page', $page) // Paginacija->get()
            ->map(function($message) use ($contactId) {
                // Dodajemo broj nepročitanih poruka kao zaseban ključ
                $message->unReadMessages = Message::countUnreadForSender(auth()->id(), $contactId, $message->service_id);
                return $message;
            });


        return response()->json([
            'messages' => $messages,
            'blockedByYou' => $blockedByYou, // Dodajte blokirani status u odgovor
            'blockedByHim' => $blockedByHim, // Dodajte blokirani status u odgovor
        ]);
    }

    public function getLastMessageByService(Request $request)
    {
        $serviceId = $request->input('service_id');
        $contactId = $request->input('contact_id');

        // Dohvati poslednju poruku
        $lastMessage = Message::where(function($query) use ($contactId) {
                        $query->where('sender_id', auth()->id())
                              ->where('receiver_id', $contactId);
                    })
                    ->orWhere(function($query) use ($contactId) {
                        $query->where('sender_id', $contactId)
                              ->where('receiver_id', auth()->id());
                    })
                    ->where('service_id', $serviceId)
                    ->orderBy('created_at', 'desc')
                    ->first();

        // Izračunaj broj nepročitanih poruka
        $unreadCount = Message::where('service_id', $serviceId)
            ->where('sender_id', $contactId)
            ->where('receiver_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        if ($lastMessage) {
            return response()->json([
                'last_message_time' => $lastMessage->created_at->format('Y-m-d H:i:s'),
                'unread_count' => $unreadCount
            ]);
        } else {
            return response()->json([
                'last_message_time' => null,
                'unread_count' => $unreadCount
            ]);
        }
    }

    public function getMessagesComplaints(Request $request)
    {
        $serviceId = $request->input('service_id');
        $page = $request->input('page', 1);  // Novi parametar za stranicu

        // Proverite da li je korisnik autorizovan da vidi ove poruke
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Dobijte sveže poruke iz baze
        $messages = Message::where('service_id', $serviceId)
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
            ->paginate(10, ['*'], 'page', $page); // Paginacija->get()

        return response()->json([
            'messages' => $messages
        ]);
    }

    public function blockUser($blockedUserId)
    {
        $user = auth()->user();
        // Proverite da li je već blokirao tog korisnika
        if ($user->blockedUsers()->where('blocked_user_id', $blockedUserId)->exists()) {
            return response()->json(['message' => 'Korisnik je već blokiran.'], 400);
        }

        // Blokirajte korisnika
        $user->blockedUsers()->create(['blocked_user_id' => $blockedUserId]);

        // Provera blokiranja
        $blockedByYou = BlockedUser::where('user_id', auth()->id())
                           ->where('blocked_user_id', $blockedUserId)
                           ->exists();

        $blockedByHim = BlockedUser::where('user_id', $blockedUserId)
                           ->where('blocked_user_id', auth()->id())
                           ->exists();


        return response()->json([
            'success' => true,
            'blockedByYou' => $blockedByYou,
            'blockedByHim' => $blockedByHim,
            'message' => 'Korisnik je uspešno blokiran.'
        ], 200);
    }

    public function unblockUser($blockedUserId)
    {
        $user = auth()->user();

        // Proverite da li je korisnik koji pokušava da odblokira već blokirao tog korisnika
        $blockedUser = $user->blockedUsers()->where('blocked_user_id', $blockedUserId)->first();

        if (!$blockedUser) {
            return response()->json(['message' => 'Nema korisnika koji je blokiran sa tim ID-jem.'], 404);
        }

        // Uklonite blokadu
        $blockedUser->delete();

        // Provera blokiranja
        $blockedByYou = BlockedUser::where('user_id', auth()->id())
                           ->where('blocked_user_id', $blockedUserId)
                           ->exists();

        $blockedByHim = BlockedUser::where('user_id', $blockedUserId)
                           ->where('blocked_user_id', auth()->id())
                           ->exists();

        return response()->json([
            'success' => true,
            'blockedByYou' => $blockedByYou,
            'blockedByHim' => $blockedByHim,
            'message' => 'Korisnik je uspešno odblokiran.'
        ], 200);
    }
}
