<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiatPayout extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'amount',
        'payment_method',
        'payment_details',
        'transaction_id',
        'card_number', // Dodaj polje za karticu
        'card_holder_name', // Dodaj polje za ime vlasnika
        'card_expiry_date', // Dodaj polje za datum isteka
        'request_date',
        'deposits',
        'status',
    ];

    protected $casts = [
        'request_date' => 'date',
        'amount' => 'decimal:2',
        'deposits' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
