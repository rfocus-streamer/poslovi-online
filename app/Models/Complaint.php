<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'message',
        'attachment',
        'status',
        'admin_decision',
    ];

    // Relacija ka projektu
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Relacija ka participantu
    public function participant()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //Relacija ka servisu
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
