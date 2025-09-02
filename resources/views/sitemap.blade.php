<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('packages.index') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>{{ route('subscriptions.index') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    @foreach($categories as $category)
    <url>
        <loc>{{ route('home', ['category' => $category->name]) }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach
    @foreach($services as $service)
    <url>
        <loc>{{ route('services.show', $service->id) }}</loc>
        <lastmod>{{ $service->updated_at->format('Y-m-d') }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
        @if($service->serviceImages->count() > 0)
        <image:image>
            <image:loc>{{ asset('storage/'.$service->serviceImages->first()->path) }}</image:loc>
            <image:title>{{ $service->title }}</image:title>
        </image:image>
        @endif
    </url>
    @endforeach
</urlset>
