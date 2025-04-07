<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run()
    {
        $packages = [
            ['name' => 'Start paket', 'slug' => 'start', 'description' => '1 € (1 usluga)', 'price' => 1, 'quantity' => 1],
            ['name' => 'Pro paket', 'slug' => 'pro', 'description' => '3 € (5 usluga)', 'price' => 3, 'quantity' => 5],
            ['name' => 'Premium paket', 'slug' => 'premium', 'description' => '5 € (10 usluga)', 'price' => 5, 'quantity' => 10],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}
