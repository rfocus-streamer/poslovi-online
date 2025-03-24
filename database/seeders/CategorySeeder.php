<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // Isključi proveru stranih ključeva zbog truncate operacije
        //DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        //Category::truncate();
        //DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Glavne kategorije
        $grafickiDizajn = Category::create(['name' => 'GRAFIČKI DIZAJN']);
        $programiranje = Category::create(['name' => 'PROGRAMIRANJE']);
        $digitalniMarketing = Category::create(['name' => 'DIGITALNI MARKETING']);
        $fotoVideo = Category::create(['name' => 'FOTO I VIDEO']);
        $pisanjePrevodjenje = Category::create(['name' => 'PISANJE & PREVOĐENJE']);
        $muzikaAudio = Category::create(['name' => 'MUZIKA & AUDIO']);
        $biznis = Category::create(['name' => 'BIZNIS']);
        $ostalo = Category::create(['name' => 'OSTALO']);

        // Podkategorije za GRAFIČKI DIZAJN
        $grafickePodkategorije = [
            'Logo dizajn',
            'Postovi za društvene mreže',
            'Dizajn za Newsletter',
            'Prezentacije',
            'Ilustracije dečijih knjiga',
            'Portreti i karikature',
            'Story Boards',
            'Dizajn web sajtova',
            'Dizajn aplikacija',
            'UZ dizajn',
            'Dizajn ikonica',
            'Dizajn vizit karti',
            'Dizajn flajera',
            'Dizajn brošura i kataloga',
            'Dizajn cenovnika',
            'Dizajn knjiga',
            'Dizajn eksterijera',
            'Dizajn enterijera',
            'Dizajn tetovaža',
            'Modni dizajn',
            '3D dizajn'
        ];
        foreach ($grafickePodkategorije as $kategorija) {
            Category::create(['name' => $kategorija, 'parent_id' => $grafickiDizajn->id]);
        }

        // Podkategorije za PROGRAMIRANJE
        $programerskePodkategorije = [
            'Izrada WordPress web-sajtova',
            'Izrada Shopify web-sajtova',
            'Izrada Wix web-sajtova',
            'Izrada Webflow web-sajtova',
            'Izrada PHP web-sajtova',
            'Izrada sajtova za droppshipping',
            'Izrada Android aplikacija',
            'Izrada IOS aplikacija',
            'Izrada web-aplikacija',
            'Testiranje web-sajtova i aplikacija',
            'Bug fixes',
            'Back Up i migracija'
        ];
        foreach ($programerskePodkategorije as $kategorija) {
            Category::create(['name' => $kategorija, 'parent_id' => $programiranje->id]);
        }

        // Podkategorije za DIGITALNI MARKETING
        $marketingPodkategorije = [
            'Vođenje društvenih mreža',
            'Kreiranje i vođenje YouTube kanala',
            'Video marketing',
            'E-Commerce marketing',
            'Email marketing',
            'Affiliate marketing',
            'Shopify marketing',
            'Promocija podkasta',
            'Promocija knjiga',
            'Web analitike',
            'Odnosi sa javnošću',
            'Marketing savetovanje',
            'Google My Business',
            'FB i Instagram reklamiranje',
            'Google reklamiranje',
            'SEO Optimizacija'
        ];
        foreach ($marketingPodkategorije as $kategorija) {
            Category::create(['name' => $kategorija, 'parent_id' => $digitalniMarketing->id]);
        }

        // Podkategorije za FOTO I VIDEO
        $fotoVideoPodkategorije = [
            'Video montaža',
            'Specijalni efekti',
            'Intro & Outro',
            'Titlovi',
            'Animacija',
            'Editovanje fotografija',
            'NFT animacija',
            'Izrada trejlera'
        ];
        foreach ($fotoVideoPodkategorije as $kategorija) {
            Category::create(['name' => $kategorija, 'parent_id' => $fotoVideo->id]);
        }

        // Podkategorije za PISANJE & PREVOĐENJE
        $pisanjePodkategorije = [
            'Pisanje blogova',
            'Pisanje sadržaja za web-sajtove',
            'Pisanje sadržaja za drušvene mreže',
            'Lektura',
            'Prevođenje',
            'CV pisanje',
            'Kreiranje opisa proizvoda',
            'Pisanje članaka za novine',
            'Transkripcija'
        ];
        foreach ($pisanjePodkategorije as $kategorija) {
            Category::create(['name' => $kategorija, 'parent_id' => $pisanjePrevodjenje->id]);
        }

        // Podkategorije za MUZIKA & AUDIO
        $audioPodkategorije = [
            'Komponovanje muzike',
            'Džinglovi',
            'Miksovanje i mastering',
            'Audio dizajn',
            'Audio montaža',
            'Voice over',
            'Snimanje audio knjiga'
        ];
        foreach ($audioPodkategorije as $kategorija) {
            Category::create(['name' => $kategorija, 'parent_id' => $muzikaAudio->id]);
        }

        // Podkategorije za BIZNIS
        $biznisPodkategorije = [
            'Virtuelni Asistent',
            'Menadžer projekta',
            'HR Konsultant',
            'Istraživanje tržišta',
            'Amazon Ekspert',
            'Shopify Ekspert',
            'Etsy Ekspert',
            'Otvaranje firme',
            'Izrada biznis planova',
            'Knjigovodstvene usluge',
            'Finansijsko savetovanje'
        ];
        foreach ($biznisPodkategorije as $kategorija) {
            Category::create(['name' => $kategorija, 'parent_id' => $biznis->id]);
        }

        // Podkategorije za OSTALO
        $ostaloPodkategorije = [
            'Putovanja',
            'Astrologija & Spiritualnost',
            'Izrada planova ishrane',
            'Izrada planova treninga',
            'Izrada porodičnog stabla',
            'Igrice'
        ];
        foreach ($ostaloPodkategorije as $kategorija) {
            Category::create(['name' => $kategorija, 'parent_id' => $ostalo->id]);
        }
    }
}
