<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForcedService extends Model
{
    protected $table = 'forced_services';
    protected $fillable = ['service_id', 'priority'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
