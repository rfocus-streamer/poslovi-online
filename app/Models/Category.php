<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * Dohvati podkategorije za ovu kategoriju.
     */
    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Dohvati glavnu kategoriju za ovu podkategoriju.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}
