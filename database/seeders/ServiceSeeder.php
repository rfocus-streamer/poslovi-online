<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        $services = [
            [
                'user_id' => 2, // Prodavac 1
                'category_id' => 2, // PROGRAMIRANJE
                'subcategory_id' => 1, // Izrada WordPress web-sajtova
                'title' => 'Izrada WordPress sajta',
                'description' => 'Profesionalni WordPress sajtovi po vašim željama.',
                'basic_price' => 5000,
                'basic_delivery_days' => 5,
                'basic_inclusions' => '1 stranica, kontakt forma',
                'standard_price' => 10000,
                'standard_delivery_days' => 7,
                'standard_inclusions' => '5 stranica, SEO optimizacija',
                'premium_price' => 15000,
                'premium_delivery_days' => 10,
                'premium_inclusions' => '10 stranica, SEO, hosting'
            ],
            [
                'user_id' => 2, // Prodavac 1
                'category_id' => 4, // FOTO I VIDEO
                'subcategory_id' => 4, // Premiere Pro
                'title' => 'Video montaža za YouTube',
                'description' => 'Profesionalna montaža video sadržaja za YouTube kanale.',
                'basic_price' => 8000,
                'basic_delivery_days' => 3,
                'basic_inclusions' => 'Osnovna montaža do 5 minuta',
                'standard_price' => 15000,
                'standard_delivery_days' => 5,
                'standard_inclusions' => 'Montaža do 10 minuta sa efektima',
                'premium_price' => 25000,
                'premium_delivery_days' => 7,
                'premium_inclusions' => 'Montaža do 30 minuta sa kompletnim postprodukcijom'
            ],
            [
                'user_id' => 3, // Prodavac 2
                'category_id' => 1, // Graphic Design
                'subcategory_id' => 1, // Logo Design
                'title' => 'Dizajn logoa',
                'description' => 'Kreiranje unikatnih logoa za vaš brend.',
                'basic_price' => 3000,
                'basic_delivery_days' => 3,
                'basic_inclusions' => '1 koncept, 2 revizije',
                'standard_price' => 6000,
                'standard_delivery_days' => 5,
                'standard_inclusions' => '2 koncepta, 4 revizije',
                'premium_price' => 10000,
                'premium_delivery_days' => 7,
                'premium_inclusions' => '3 koncepta, neograničene revizije'
            ],
            [
                'user_id' => 4, // Prodavac 3
                'category_id' => 1, // Web Development
                'subcategory_id' => 2, // JavaScript
                'title' => 'Single Page Aplikacija',
                'description' => 'Razvoj modernih SPA aplikacija u Reactu.',
                'basic_price' => 15000,
                'basic_delivery_days' => 10,
                'basic_inclusions' => 'Osnovna funkcionalnost',
                'standard_price' => 30000,
                'standard_delivery_days' => 15,
                'standard_inclusions' => 'Autentifikacija i API integracija',
                'premium_price' => 50000,
                'premium_delivery_days' => 25,
                'premium_inclusions' => 'Full-stack aplikacija sa admin panelom'
            ],
            [
                'user_id' => 5, // Prodavac 4
                'category_id' => 4, // Marketing
                'subcategory_id' => 9, // Social Media
                'title' => 'Social Media Marketing',
                'description' => 'Upravljanje društvenim mrežama za vaš biznis.',
                'basic_price' => 20000,
                'basic_delivery_days' => 7,
                'basic_inclusions' => '3 posta nedeljno',
                'standard_price' => 35000,
                'standard_delivery_days' => 7,
                'standard_inclusions' => '5 postova nedeljno + analitika',
                'premium_price' => 50000,
                'premium_delivery_days' => 7,
                'premium_inclusions' => '10 postova nedeljno + paid ads'
            ]
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
