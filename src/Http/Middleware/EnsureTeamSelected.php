<?php

namespace Electrik\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeamSelected
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! method_exists($user, 'teams')) {
            return $next($request);
        }

        if ($user->teams()->count() === 0) {
            return redirect()->route('teams.create');
        }

        if (! $user->currentTeam) {
            $first = $user->teams()->first();

            if ($first) {
                $user->switchTeam($first);
                $user->refresh();
            }
        }

        if ($user->current_team_id) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($user->current_team_id);
        }

        return $next($request);
    }
}
