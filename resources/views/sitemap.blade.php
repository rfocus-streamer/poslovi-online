<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    @foreach($services as $service)
        @if($service->visible && $service->visible_expires_at && $service->visible_expires_at->gte(now()))
            <url>
                <loc>{{ url('/ponuda/' . $service->id . '-' . \Illuminate\Support\Str::slug($service->title)) }}</loc>
                <lastmod>{{ $service->updated_at->toAtomString() }}</lastmod>
                <changefreq>weekly</changefreq>
                <priority>0.8</priority>
            </url>
        @endif
    @endforeach
</urlset>
