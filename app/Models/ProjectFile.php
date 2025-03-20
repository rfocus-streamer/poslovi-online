<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectFile extends Model
{
    use HasFactory;
    protected $fillable = ['project_id', 'user_id', 'file_path', 'original_name', 'description'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
