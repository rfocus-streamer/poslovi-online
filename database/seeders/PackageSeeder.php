<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run()
    {
        $packages = [
            // Godišnji paketi (sa 20% popusta)
            ['name' => 'Start paket', 'slug' => 'start-yearly', 'description' => '1 usluga', 'price' => 12 * (1 - 0.20), 'duration' => 'yearly', 'quantity' => 1],
            ['name' => 'Pro paket', 'slug' => 'pro-yearly', 'description' => '5 usluga', 'price' => 36 * (1 - 0.20), 'duration' => 'yearly', 'quantity' => 5],
            ['name' => 'Premium paket', 'slug' => 'premium-yearly', 'description' => '10 usluga', 'price' => 60 * (1 - 0.20), 'duration' => 'yearly', 'quantity' => 10],

            // Mesečni paketi
            ['name' => 'Start paket', 'slug' => 'start', 'description' => '1 usluga', 'price' => 1, 'duration' => 'monthly', 'quantity' => 1],
            ['name' => 'Pro paket', 'slug' => 'pro', 'description' => '5 usluga', 'price' => 3, 'duration' => 'monthly', 'quantity' => 5],
            ['name' => 'Premium paket', 'slug' => 'premium', 'description' => '10 usluga', 'price' => 5, 'duration' => 'monthly', 'quantity' => 10],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}
