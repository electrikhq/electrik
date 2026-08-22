<?php

namespace Electrik\Http\Middleware;

use Closure;
use Electrik\Models\StripePlan;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('electrik.billing.require_subscription', false)) {
            return $next($request);
        }

        $user = $request->user();
        $team = $user?->currentTeam;

        if (! $user || ! $team) {
            return redirect()->route('teams.index');
        }

        $name = config('electrik.billing.subscription_name', 'electrik');
        $subscription = $team->subscription($name);

        if ($subscription && ($subscription->active() || $subscription->onTrial() || $subscription->onGracePeriod())) {
            return $next($request);
        }

        $planModel = config('electrik.billing.plan_model', StripePlan::class);
        if ($planModel::query()->where('price', 0)->exists()) {
            return $next($request);
        }

        return redirect()
            ->route('billing.plans')
            ->with('error', __('Please choose a subscription plan to continue.'));
    }
}
