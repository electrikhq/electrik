<?php

namespace Electrik\Actions\Billing;

use Electrik\Models\StripePlan;
use Electrik\Models\Team;
use Laravel\Cashier\Checkout;
use Laravel\Cashier\Subscription;

class CreateSubscription
{
    /**
     * Start a subscription for the team.
     *
     * Paid plans use Stripe Checkout (returns Checkout). Free plans create immediately (returns Subscription).
     */
    public function execute(Team $team, StripePlan $plan): Checkout|Subscription
    {
        $name = config('electrik.billing.subscription_name', 'electrik');

        if ($existing = $team->subscription($name)) {
            if ($existing->active() || $existing->onTrial() || $existing->onGracePeriod()) {
                $existing->swap($plan->stripe_price_id);

                return $team->subscription($name);
            }
        }

        $builder = $team->newSubscription($name, $plan->stripe_price_id);

        if ($plan->isFree() && ! config('electrik.billing.cc_required_for_free_plan', false)) {
            return $builder->create();
        }

        return $builder->checkout([
            'success_url' => route('billing.index', absolute: true).'?checkout=success&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('billing.plans', absolute: true).'?checkout=cancelled',
        ]);
    }
}
