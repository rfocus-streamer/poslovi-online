<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;
    // app/Models/Commission.php
    protected $fillable = [
        'project_id',
        'seller_id',
        'buyer_id',
        'amount',
        'percentage',
        'commission_amount',
        'seller_amount'
    ];

    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function seller() {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function buyer() {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
