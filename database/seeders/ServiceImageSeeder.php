<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceImage;

class ServiceImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = Service::all();

        foreach ($services as $index => $service) {
            ServiceImage::create([
                    'service_id' => $service->id,
                    'image_path' => 'service'.$service->id.'.jpg'
            ]);
        }
    }
}
