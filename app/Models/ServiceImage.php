<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceImage extends Model
{
    protected $fillable = ['service_id', 'image_path'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
