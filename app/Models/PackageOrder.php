<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageOrder extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'package_id', 'amount', 'expires_at'];

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
}
