<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailNotification extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'type',
        'last_sent_at',
        'sent_count'
    ];

    protected $casts = [
        'last_sent_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
