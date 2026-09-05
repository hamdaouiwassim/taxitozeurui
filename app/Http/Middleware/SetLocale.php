<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();
        $locale = str_ends_with((string) $route?->getName(), '.fr') ? 'fr' : config('app.locale');

        if (in_array($locale, ['en', 'fr'], true)) {
            App::setLocale($locale);
            Carbon::setLocale($locale);
        }

        URL::defaults(['locale' => $locale]);

        return $next($request);
    }
}
