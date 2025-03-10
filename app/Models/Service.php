<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $with = ['category', 'subcategory', 'user'];
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
}
