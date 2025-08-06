<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Subscription extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'plan_id', 'status', 'gateway', 'subscription_id', 'stripe_session_id', 'ends_at', 'amount', 'expires_at'];

    public function package()
    {
        return $this->belongsTo(Package::class, 'plan_id');
    }

    /**
     * Scope za aktivne pretplate
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', 'active');
    }

    /**
     * Scope za filtriranje po paketu
     */
    public function scopeForPackage(Builder $query, int $packageId): Builder
    {
        return $query->where('plan_id', $packageId); // Proverite da li je kolona tačno nazvana
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
