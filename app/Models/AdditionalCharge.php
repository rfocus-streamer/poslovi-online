<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdditionalCharge extends Model
{
    protected $fillable = [
        'project_id',
        'seller_id',
        'amount',
        'reason',
    ];

    // Relacija ka projektu
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Relacija ka prodavcu
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
