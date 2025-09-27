<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'content',
        'sender_id',
        'receiver_id',
        'service_id',
        'read_at',
        'attachment_path',
        'attachment_name',
        'type',
        'call_data'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public static function countUnreadForSender($receiver_id, $sender_id, $service_id)
    {
        return self::where('receiver_id', $receiver_id)
                   ->where('sender_id', $sender_id)
                   ->where('service_id', $service_id)
                   ->whereNull('read_at')
                   ->count();
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
