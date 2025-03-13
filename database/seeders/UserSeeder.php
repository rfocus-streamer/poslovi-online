<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::create([
            'firstname' => 'Admin',
            'lastname' => 'Admin',
            'email' => 'admin@poslovionline.com',
            'phone' => '123456789',
            'payment_method' => 'PayPal',
            'deposits' => 10000,
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_verified' => true,
            'avatar' => 'user.jpg',
            'stars' => 4
        ]);

        // Prodavci
        $sellers = [
            [
                'firstname' => 'Prodavac',
                'lastname' => '1',
                'email' => 'prodavac1@poslovionline.com',
                'phone' => '987654321',
                'payment_method' => 'Bank Transfer',
                'deposits' => 10000,
                'password' => Hash::make('prodavac123'),
                'role' => 'seller',
                'is_verified' => true,
                'seller_level' => 1,
                'avatar' => 'user.jpg',
                'stars' => 5
            ],
            [
                'firstname' => 'Prodavac',
                'lastname' => '2',
                'email' => 'prodavac2@poslovionline.com',
                'phone' => '111222333',
                'payment_method' => 'Payoneer',
                'deposits' => 10000,
                'password' => Hash::make('prodavac123'),
                'role' => 'seller',
                'is_verified' => true,
                'seller_level' => 2,
                'avatar' => 'user.jpg',
                'stars' => 4
            ],
            [
                'firstname' => 'Prodavac 3',
                'lastname' => '3',
                'email' => 'prodavac3@poslovionline.com',
                'phone' => '444555666',
                'payment_method' => 'Wise',
                'deposits' => 10000,
                'password' => Hash::make('prodavac123'),
                'role' => 'seller',
                'is_verified' => true,
                'seller_level' => 1,
                'avatar' => 'user.jpg',
                'stars' => 3
            ]
        ];

        foreach ($sellers as $seller) {
            User::create($seller);
        }

        // Kupac
        User::create([
            'firstname' => 'Kupac',
            'lastname' => '1',
            'email' => 'kupac1@poslovionline.com',
            'phone' => '555555555',
            'payment_method' => 'Credit Card',
            'deposits' => 10000,
            'password' => Hash::make('kupac123'),
            'role' => 'buyer',
            'is_verified' => true,
            'avatar' => 'user.jpg',
            'stars' => 5
        ]);
    }
}
