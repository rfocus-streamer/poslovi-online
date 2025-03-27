<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ServiceSeeder::class,
            //SubscriptionSeeder::class,
            AffiliateSeeder::class,
            ServiceImageSeeder::class, // Dodajte ovu liniju
            ReviewSeeder::class,
            PackageSeeder::class,
        ]);
    }
}
