<?php

namespace Electrik\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Require the current Sanctum token to have one of the given abilities.
 * Tokens with "*" always pass. Session/web auth (no token) passes through.
 */
class EnsureTokenAbility
{
    public function handle(Request $request, Closure $next, string ...$abilities): Response
    {
        $user = $request->user();

        if (! $user || ! method_exists($user, 'currentAccessToken')) {
            return $next($request);
        }

        $token = $user->currentAccessToken();

        if (! $token || ! method_exists($token, 'can')) {
            return $next($request);
        }

        if ($token->can('*')) {
            return $next($request);
        }

        foreach ($abilities as $ability) {
            if ($token->can($ability)) {
                return $next($request);
            }
        }

        abort(Response::HTTP_FORBIDDEN, __('This token is missing the required ability.'));
    }
}
