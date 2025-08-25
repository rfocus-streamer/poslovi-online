<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivilegedCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'buyer_commission',
        'seller_commission'
    ];

    protected $casts = [
        'buyer_commission' => 'decimal:2',
        'seller_commission' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
