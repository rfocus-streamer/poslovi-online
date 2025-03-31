<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run()
    {
        $packages = [
            ['name' => 'Start paket', 'slug' => 'start', 'description' => '100 din (1 usluga)', 'price' => 100, 'quantity' => 1],
            ['name' => 'Pro paket', 'slug' => 'pro', 'description' => '300 din (5 usluga)', 'price' => 300, 'quantity' => 5],
            ['name' => 'Premium paket', 'slug' => 'premium', 'description' => '500 din (10 usluga)', 'price' => 500, 'quantity' => 10],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}
