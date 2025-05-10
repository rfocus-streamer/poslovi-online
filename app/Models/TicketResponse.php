<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketResponse extends Model
{
    use HasFactory;
    protected $fillable = ['content', 'attachment', 'ticket_id', 'user_id', 'read_at'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope za nepročitane odgovore
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    // Helper metoda za proveru
    public function isUnread()
    {
        return is_null($this->read_at);
    }
}
