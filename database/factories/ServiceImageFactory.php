<?php

namespace Database\Factories;

use App\Models\ServiceImage;
use Illuminate\Database\Seeder;

class ServiceImageSeeder extends Seeder
{
    public function run()
    {
        // Kreiraj 5-15 slika po svakoj usluzi
        Service::each(function ($service) {
            ServiceImage::factory()
                ->count(rand(5, 15))
                ->create([
                    'service_id' => $service->id,
                    'image_path' => 'service1.jpg'
                ]);
        });
    }
}
