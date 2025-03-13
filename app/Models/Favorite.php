<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
