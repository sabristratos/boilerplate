<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Facades\Settings;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $availableLocales = config('app.available_locales', ['en']); // Default to 'en' if not configured
        $defaultLocale = Settings::get('default_language', config('app.fallback_locale', 'en'));

        $locale = Session::get('locale', $defaultLocale);

        if (!array_key_exists($locale, $availableLocales)) {
            $locale = $defaultLocale;
        }
        
        if (!array_key_exists($locale, $availableLocales)) { // Fallback if default from settings is also not in available_locales
            $locale = config('app.fallback_locale', 'en');
        }

        App::setLocale($locale);
        Session::put('locale', $locale); // Store the determined locale in session

        return $next($request);
    }
}
