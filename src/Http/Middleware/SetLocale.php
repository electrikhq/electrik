<?php

namespace Electrik\Http\Middleware;

use Electrik\Support\Locales;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = config('app.locale', 'en');

        $user = $request->user() ?? Auth::user();

        if ($user && Locales::isSupported($user->locale ?? null)) {
            $locale = $user->locale;
        }

        if (! Locales::isSupported($locale)) {
            $locale = Locales::isSupported((string) config('app.fallback_locale'))
                ? (string) config('app.fallback_locale')
                : 'en';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
