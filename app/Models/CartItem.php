<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'seller_id',
        'service_id',
        'quantity',
        'package'
    ];

    // Relacija sa User modelom
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacija sa Service modelom
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
