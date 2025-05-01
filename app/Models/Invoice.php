<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',       // Dodajemo number u fillable
        'user_id',
        'issue_date',
        'status',
        'total',
        'client_info',
        'items',
        'payment_method'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'client_info' => 'json',
        'items' => 'json',
    ];
}
