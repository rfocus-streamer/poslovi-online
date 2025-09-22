<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailMessageNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message_id',
        'last_sent_at'
    ];
}
