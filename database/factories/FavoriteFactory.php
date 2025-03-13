<?php

namespace Database\Factories;

use App\Models\Favorite;
use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavoriteFactory extends Factory
{
    protected $model = Favorite::class;

    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'service_id' => Service::inRandomOrder()->first()->id,
        ];
    }
}
