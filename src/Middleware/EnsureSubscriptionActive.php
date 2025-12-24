<?php

namespace Electrik\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->currentTeam) {
            return redirect()->route('login');
        }

        $team = $user->currentTeam;
        $subscriptionName = config('electrik.default_subscription_name', 'electrik');
        $subscription = $team->subscription($subscriptionName);

        // If no subscription and not on a free plan, redirect to billing
        if (!$subscription || $subscription->canceled()) {
            // Check if there's a free plan available
            $freePlan = \App\Models\StripePlan::where('price', 0)->first();
            
            if (!$freePlan) {
                return redirect()->route('billing.index')
                    ->with('error', 'Please select a subscription plan to continue.');
            }
        }

        return $next($request);
    }
}

