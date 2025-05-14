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
                'subcategory_id' => 30, // Izrada WordPress web-sajtova
                'title' => 'Izrada WordPress sajta',
                'description' => 'Profesionalni WordPress sajtovi po vašim željama.',
                'basic_price' => 500,
                'basic_delivery_days' => 5,
                'basic_inclusions' => '1 stranica, kontakt forma',
                'standard_price' => 1000,
                'standard_delivery_days' => 7,
                'standard_inclusions' => '5 stranica, SEO optimizacija',
                'premium_price' => 1500,
                'premium_delivery_days' => 10,
                'premium_inclusions' => '10 stranica, SEO, hosting',
                //'visible' => true
            ],
            [
                'user_id' => 2, // Prodavac 1
                'category_id' => 4, // FOTO I VIDEO
                'subcategory_id' => 58, // Video montaža
                'title' => 'Video montaža za YouTube',
                'description' => 'Profesionalna montaža video sadržaja za YouTube kanale.',
                'basic_price' => 800,
                'basic_delivery_days' => 3,
                'basic_inclusions' => 'Osnovna montaža do 5 minuta',
                'standard_price' => 1500,
                'standard_delivery_days' => 5,
                'standard_inclusions' => 'Montaža do 10 minuta sa efektima',
                'premium_price' => 2500,
                'premium_delivery_days' => 7,
                'premium_inclusions' => 'Montaža do 30 minuta sa kompletnim postprodukcijom',
                //'visible' => true
            ],
            [
                'user_id' => 3, // Prodavac 2
                'category_id' => 1, // Graphic Design
                'subcategory_id' => 9, // Logo Design
                'title' => 'Dizajn logoa',
                'description' => 'Kreiranje unikatnih logoa za vaš brend.',
                'basic_price' => 300,
                'basic_delivery_days' => 3,
                'basic_inclusions' => '1 koncept, 2 revizije',
                'standard_price' => 600,
                'standard_delivery_days' => 5,
                'standard_inclusions' => '2 koncepta, 4 revizije',
                'premium_price' => 1000,
                'premium_delivery_days' => 7,
                'premium_inclusions' => '3 koncepta, neograničene revizije',
                //'visible' => true
            ],
            [
                'user_id' => 4, // Prodavac 3
                'category_id' => 2, // PROGRAMIRANJE
                'subcategory_id' => 38, // Izrada web-aplikacija
                'title' => 'Single Page Aplikacija',
                'description' => 'Razvoj modernih SPA aplikacija u Reactu.',
                'basic_price' => 1500,
                'basic_delivery_days' => 10,
                'basic_inclusions' => 'Osnovna funkcionalnost',
                'standard_price' => 3000,
                'standard_delivery_days' => 15,
                'standard_inclusions' => 'Autentifikacija i API integracija',
                'premium_price' => 5000,
                'premium_delivery_days' => 25,
                'premium_inclusions' => 'Full-stack aplikacija sa admin panelom',
                //'visible' => true
            ],
            [
                'user_id' => 3, // Prodavac 2
                'category_id' => 3, // Digitalni Marketing
                'subcategory_id' => 42, // Vođenje društvenih mreža
                'title' => 'Social Media Marketing',
                'description' => 'Upravljanje društvenim mrežama za vaš biznis.',
                'basic_price' => 2000,
                'basic_delivery_days' => 7,
                'basic_inclusions' => '3 posta nedeljno',
                'standard_price' => 3500,
                'standard_delivery_days' => 7,
                'standard_inclusions' => '5 postova nedeljno + analitika',
                'premium_price' => 5000,
                'premium_delivery_days' => 7,
                'premium_inclusions' => '10 postova nedeljno + paid ads',
                //'visible' => true
            ]
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
