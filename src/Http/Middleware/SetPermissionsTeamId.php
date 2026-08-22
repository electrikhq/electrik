<?php

namespace Electrik\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

class SetPermissionsTeamId
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->current_team_id) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($user->current_team_id);
        }

        return $next($request);
    }
}
