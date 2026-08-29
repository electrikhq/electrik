<?php

namespace Electrik\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeamIpAllowed
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $team = $user?->currentTeam;

        if (! $team || ! method_exists($team, 'allowsIp')) {
            return $next($request);
        }

        if ($team->allowsIp($request->ip())) {
            return $next($request);
        }

        abort(403, __('Your IP address is not allowed for this team.'));
    }
}
