<?php

namespace App\Support;

use RalphJSmit\Laravel\SEO\Support\SEOData as BaseSEOData;

class SEOData extends BaseSEOData
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $url = null,
        public ?string $image = null,
        public array $alternateUrls = [],
        public array $breadcrumbs = []
    ) {
        parent::__construct(
            title: $title ?? 'Poslovi Online | Platforma za freelance usluge',
            description: $description ?? 'Pronađite ili ponudite digitalne usluge na najbržoj domaćoj freelance platformi.',
            url: $url ?? url('/'),
            image: $image ?? asset('images/logo.png')
        );
    }
}
