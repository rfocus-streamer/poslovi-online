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
            ServiceImageSeeder::class,
            PackageSeeder::class,
            ReviewSeeder::class, //poslednji seeder zbog Class "Faker\Factory" not found
        ]);
    }
}
