<?php

namespace Electrik\Http\Middleware;

use Closure;
use Electrik\Support\BillingStatus;
use Electrik\Models\StripePlan;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! BillingStatus::subscriptionRequired()) {
            return $next($request);
        }

        $user = $request->user();
        $team = $user?->currentTeam;

        if (! $user || ! $team) {
            return redirect()->route('teams.index');
        }

        if (BillingStatus::teamHasAccess($team)) {
            return $next($request);
        }

        return redirect()
            ->route('billing.plans')
            ->with('error', __('Please choose a subscription plan to continue.'));
    }
}
