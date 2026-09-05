@php
    $currentLocale = app()->getLocale();
    $otherLocale = $currentLocale === 'fr' ? 'en' : 'fr';
    $altLabel = strtoupper($otherLocale);
    $route = request()->route();
    $baseName = $route?->getName();

    if ($baseName !== null && in_array(str_ends_with($baseName, '.fr') ? substr($baseName, 0, -3) : $baseName, ['home', 'login', 'drivers.show', 'drivers.reviews.store'], true)) {
        $cleanName = str_ends_with($baseName, '.fr') ? substr($baseName, 0, -3) : $baseName;
        $url = $otherLocale === 'fr'
            ? route($cleanName.'.fr', $route->parameters())
            : route($cleanName, $route->parameters());
    } else {
        $url = $otherLocale === 'fr' ? url('/fr') : url('/');
    }
@endphp
<a href="{{ $url }}" class="lang-switch-link" title="@lang('Switch to :locale', ['locale' => $altLabel])">{{ $altLabel }}</a>