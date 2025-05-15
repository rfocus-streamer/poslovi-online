<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_number', 'service_id', 'quantity', 'package', 'description', 'buyer_id', 'seller_id', 'status', 'reserved_funds', 'start_date', 'end_date'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($project) {
            // Brisanje svih fajlova iz storage-a
            foreach ($project->files as $file) {
                Storage::disk('public')->delete($file->file_path);
                $file->delete();
            }

            // Brisanje foldera project_{id}
            $folderPath = "project_files/project_{$project->id}";
            if (Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->deleteDirectory($folderPath);
            }
        });
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function files()
    {
        return $this->hasMany(ProjectFile::class);
    }

    public function additionalCharges()
    {
        return $this->hasMany(AdditionalCharge::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }
}
