<?php

namespace Database\Seeders;

use App\Models\Favorite;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    public function run()
    {
        Favorite::factory()->count(20)->create(); // Generiše 20 omiljenih servisa
    }
}
