<?php
namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class MessageSent implements ShouldBroadcast
{
    use SerializesModels;

    public $message;

    /**
     * Kreiranje nove instance događaja.
     *
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastWith()
    {
        // Ukupan broj nepročitanih poruka za korisnika
        $totalUnreadMessages = Message::where('receiver_id', $this->message->receiver_id)
        ->whereNull('read_at')
        ->count();

        // Broj nepročitanih poruka po service_id
        $unreadMessagesPerService = Message::where('receiver_id', $this->message->receiver_id)
            ->where('service_id', $this->message->service_id)
            ->whereNull('read_at')
            ->select('service_id')  // Pretpostavljam da postoji service_id u tabeli messages
            ->groupBy('service_id')
            ->selectRaw('service_id, count(*) as count')
            ->pluck('count', 'service_id');  // Vraća asocijativni niz sa service_id kao ključem i count kao vrednostima

        $totalSenderUnreadMessages = Message::where('sender_id', $this->message->sender_id)
        ->whereNull('read_at')
        ->count();

        // Ručno dodavanje dinamičkih propertija 'type' i 'call_data'
        $messageType = $this->message->type ?? 'text'; // Ako nije postavljeno, koristi default
        $callData = $this->message->call_data ?? null;

        return [
            'message' => [
                'id' => $this->message->id,
                'content' => $this->message->content,
                'sender_id' => $this->message->sender_id,
                'receiver_id' => $this->message->receiver_id,
                'service_id' => $this->message->service_id,
                'created_at' => $this->message->created_at,
                'attachment' => $this->message->attachment_path,
                'attachment_name' => $this->message->attachment_name,
                'type' => $messageType, // Dinamički property
                'call_data' => $callData, // Dinamički property
                'totalUnreadMessages' => $totalUnreadMessages,
                'totalSenderUnreadMessages' => $totalSenderUnreadMessages,
                'unreadMessagesPerService' => $unreadMessagesPerService,
                'sender' => $this->message->sender->only([
                    'firstname',
                    'lastname',
                    'avatar',
                    'stars',
                    'is_online',
                    'role'
                ])
            ]
        ];
    }

    /**
     * Kanali kojima će se događaj emitovati.
     *
     * @return Channel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('messages'); // Emituj na kanal 'messages'
    }
}
