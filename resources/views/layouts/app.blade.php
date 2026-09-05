<!DOCTYPE html>
<html lang="@yield('html_lang', str_replace('_', '-', app()->getLocale()))">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Tozeur VIP Taxi')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <meta name="description" content="@yield('meta_description', __('Book a private taxi in Tozeur, Tunisia. Verified drivers, transparent pricing, 24/7 availability, airport transfers and VIP rides. Choose your ride and chat on WhatsApp instantly.'))">
  <meta name="keywords" content="taxi Tozeur, private taxi Tunisia, airport transfer Tozeur, VIP taxi, hire driver Tozeur, taxi service Tunisia, Tozeur airport taxi">
  <meta name="robots" content="@yield('meta_robots', 'index, follow')">
  <link rel="canonical" href="@yield('canonical', url()->current())">

  @php
    $currentRoute = request()->route();
    $alternateLinks = '';
    $baseName = $currentRoute?->getName();
    $cleanName = $baseName !== null
        ? (str_ends_with($baseName, '.fr') ? substr($baseName, 0, -3) : $baseName)
        : null;

    if ($cleanName !== null && in_array($cleanName, ['home', 'drivers.show', 'login'], true)) {
        $routeParams = $currentRoute->parameters();
        $enUrl = route($cleanName, $routeParams);
        $frUrl = route($cleanName.'.fr', $routeParams);

        $alternateLinks =
            '<link rel="alternate" hreflang="en" href="'.e($enUrl).'">'.
            '<link rel="alternate" hreflang="fr" href="'.e($frUrl).'">'.
            '<link rel="alternate" hreflang="x-default" href="'.e(url('/')).'">';
    }
  @endphp
  {!! $alternateLinks !!}

  <link rel="icon" type="image/x-icon" href="/favicon.ico">

  <meta property="og:type" content="@yield('og_type', 'website')">
  <meta property="og:site_name" content="Tozeur VIP Taxi">
  <meta property="og:title" content="@yield('og_title', 'Tozeur VIP Taxi')">
  <meta property="og:description" content="@yield('og_description', __('Book a private taxi in Tozeur, Tunisia. Verified drivers, transparent pricing, 24/7 availability, airport transfers and VIP rides.'))">
  <meta property="og:url" content="@yield('og_url', url()->current())">
  <meta property="og:image" content="@yield('og_image', url('/asstes/images/og-cover.png'))">
  <meta property="og:locale" content="{{ app()->getLocale() === 'fr' ? 'fr_FR' : 'en_US' }}">

  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="@yield('og_title', 'Tozeur VIP Taxi')">
  <meta name="twitter:description" content="@yield('og_description', __('Book a private taxi in Tozeur, Tunisia. Verified drivers, transparent pricing, 24/7 availability, airport transfers and VIP rides.'))">
  <meta name="twitter:image" content="@yield('og_image', url('/asstes/images/og-cover.png'))">

  <link rel="stylesheet" href="/css/app.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  @stack('head')
  @stack('schema')
</head>
<body @stack('body-attrs')>
  @yield('content')

  <script src="/js/app.js"></script>
  @stack('scripts')
</body>
</html>
