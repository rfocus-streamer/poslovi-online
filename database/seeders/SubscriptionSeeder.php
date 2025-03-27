<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run()
    {
        Subscription::create([
            'user_id' => 2, // Prodavac 1
            'package' => 'standard',
            'amount' => 500,
            'expires_at' => now()->addMonth(),
        ]);
    }
}
