<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliatePayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'request_date',
        'status',
        'payment_method',
        'payment_details',
        'affiliate_balance'
    ];

    protected $casts = [
        'request_date' => 'date',
        'amount' => 'decimal:2',
        'affiliate_balance' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
