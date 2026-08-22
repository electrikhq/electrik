<?php

namespace Electrik\Http\Middleware;

use Closure;
use Electrik\Support\Onboarding;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || Onboarding::completed($user)) {
            return $next($request);
        }

        if (Onboarding::isExemptRoute($request->route()?->getName())) {
            return $next($request);
        }

        return redirect()->route('onboarding');
    }
}
