<?php

namespace Electrik\Http\Middleware;

use Closure;
use Electrik\Support\PlanFeatures;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlanFeature
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $team = $request->user()?->currentTeam;

        if (! $team || PlanFeatures::has($team, $feature)) {
            return $next($request);
        }

        abort(403, __('This feature is not included in your current plan.'));
    }
}
