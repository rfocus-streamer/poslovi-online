<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Cache; // Dodajte ovaj use statement

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'phone',
        'avatar',
        'role',
        'deposits',
        'package_id',
        'package_expires_at',
        'affiliate_code',
        'referred_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isSeller()
    {
        return $this->role === 'seller';
    }

    public function isBuyer()
    {
        return $this->role === 'buyer';
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function additionalCharges()
    {
        return $this->hasMany(AdditionalCharge::class, 'seller_id');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'user_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    // Svi korisnici koje je ovaj korisnik preporučio
    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    // Sve provizije koje je zaradio kao affiliate
    public function commissionsEarned()
    {
        return $this->hasMany(Affiliate::class, 'affiliate_id');
    }

    // Provizije generisane preko njegovih preporuka
    public function referralCommissions()
    {
        return $this->hasMany(Affiliate::class, 'referral_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function reviewForAuthUser($userId, $service_id)
    {
        return $this->reviews()->where('user_id', $userId)->where('service_id', $service_id)->first();
    }

    public function getIsOnlineAttribute()
    {
        return Cache::has('user-is-online-'.$this->id);
    }

    public function updateLastSeen()
    {
        $this->last_seen_at = now();
        $this->save();
        Cache::put('user-is-online-'.$this->id, true, now()->addMinutes(5));
    }
}
