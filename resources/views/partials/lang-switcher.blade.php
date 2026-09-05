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
<a href="{{ $url }}" class="lang-switch-link" title="@lang('Switch to :locale', ['locale' => $altLabel])" aria-label="@lang('Switch to :locale', ['locale' => $altLabel])">
  @if ($otherLocale === 'fr')
  <svg class="lang-flag" viewBox="0 0 60 36" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <rect width="20" height="36" fill="#0055A4"/>
    <rect x="20" width="20" height="36" fill="#FFFFFF"/>
    <rect x="40" width="20" height="36" fill="#EF4135"/>
  </svg>
  @else
  <svg class="lang-flag" viewBox="0 0 60 36" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <path d="M0,0 v36 h60 v-36 z" fill="#012169"/>
    <path d="M0,0 60,36 M60,0 0,36" stroke="#FFFFFF" stroke-width="6" fill="none"/>
    <path d="M0,0 60,36 M60,0 0,36" stroke="#C8102E" stroke-width="4" fill="none"/>
    <path d="M30,0 v36 M0,18 h60" stroke="#FFFFFF" stroke-width="10" fill="none"/>
    <path d="M30,0 v36 M0,18 h60" stroke="#C8102E" stroke-width="6" fill="none"/>
  </svg>
  @endif
</a>