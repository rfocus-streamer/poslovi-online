<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generiši sitemap.xml fajl';

    public function handle()
    {
        $sitemap = Sitemap::create()
            ->add(Url::create('/'))
            ->add(Url::create('/packages'))
            ->add(Url::create('/subscriptions'));

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generisan!');
    }
}
