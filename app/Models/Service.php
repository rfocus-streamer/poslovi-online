<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $with = ['category', 'subcategory', 'user'];
    protected $fillable = [
        'user_id',
        'category_id',
        'subcategory_id',
        'title',
        'description',
        'basic_price',
        'basic_delivery_days',
        'basic_inclusions',
        'standard_price',
        'standard_delivery_days',
        'standard_inclusions',
        'premium_price',
        'premium_delivery_days',
        'premium_inclusions',
        'visible',
        'visible_expires_at'
    ];
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceImages()
    {
        return $this->hasMany(ServiceImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
