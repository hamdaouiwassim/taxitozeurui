<?php

if (! function_exists('lroute')) {
    /**
     * Generate a URL for a route on the public site, opting into the
     * localized route (".fr" suffix) automatically based on the current locale.
     *
     * @param  array<string, mixed>  $parameters
     */
    function lroute(string $name, array $parameters = []): string
    {
        if (app()->getLocale() === 'fr') {
            $localized = $name.'.fr';
            if (app('router')->has($localized)) {
                $name = $localized;
            }
        }

        return route($name, $parameters);
    }
}
