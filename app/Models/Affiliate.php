<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Affiliate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'affiliate_id',
        'referral_id', //
        'package_id',
        'amount',
        'percentage',
        'status',
        'paid_at',
        'payment_method',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function affiliate()
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    // Promenjeno ime relacije za veću jasnoću
    public function referral()
    {
        return $this->belongsTo(User::class, 'referral_id');
    }
}
