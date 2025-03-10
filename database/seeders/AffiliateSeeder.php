<?php

namespace Database\Seeders;

use App\Models\Affiliate;
use Illuminate\Database\Seeder;

class AffiliateSeeder extends Seeder
{
    public function run()
    {
        Affiliate::create([
            'referrer_id' => 2, // Prodavac 1
            'referred_id' => 3, // Kupac 1
            'commission' => 210, // 70% od 300 RSD (standard paket)
            'is_paid' => false,
        ]);
    }
}
