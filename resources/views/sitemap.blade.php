<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
  <url>
    <loc>{{ url('/') }}</loc>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  <url>
    <loc>{{ url('/fr') }}</loc>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  @foreach ($drivers as $driver)
  @php $enUrl = route('drivers.show', ['driver' => $driver]); $frUrl = route('drivers.show.fr', ['driver' => $driver]); @endphp
  <url>
    <loc>{{ $enUrl }}</loc>
    <lastmod>{{ optional($driver->updated_at)->toAtomString() ?: now()->toAtomString() }}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
    <xhtml:link rel="alternate" hreflang="en" href="{{ $enUrl }}" />
    <xhtml:link rel="alternate" hreflang="fr" href="{{ $frUrl }}" />
  </url>
  <url>
    <loc>{{ $frUrl }}</loc>
    <lastmod>{{ optional($driver->updated_at)->toAtomString() ?: now()->toAtomString() }}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
    <xhtml:link rel="alternate" hreflang="en" href="{{ $enUrl }}" />
    <xhtml:link rel="alternate" hreflang="fr" href="{{ $frUrl }}" />
  </url>
  @endforeach
</urlset>
