<?php

namespace Electrik\Http\Middleware;

use Electrik\Models\Team;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * When the request is authenticated via a team-scoped Sanctum token,
 * bind that team as the current team and Spatie permissions context.
 */
class BindTeamFromAccessToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! method_exists($user, 'currentAccessToken')) {
            return $next($request);
        }

        $token = $user->currentAccessToken();

        if (! $token || blank($token->team_id ?? null)) {
            return $next($request);
        }

        $team = Team::query()->find($token->team_id);

        if (! $team || (method_exists($user, 'belongsToTeam') && ! $user->belongsToTeam($team))) {
            abort(Response::HTTP_FORBIDDEN, __('This token is not valid for the requested team.'));
        }

        if (method_exists($user, 'switchTeam')) {
            $user->switchTeam($team);
        }

        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($team->getKey());
        }

        return $next($request);
    }
}
