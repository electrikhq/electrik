<?php

namespace Electrik\Actions\Billing;

use Electrik\Models\StripePlan;
use Electrik\Models\Team;
use InvalidArgumentException;

class AddSubscriptionItem
{
    /**
     * Add an add-on price to the team's existing subscription.
     */
    public function execute(Team $team, StripePlan $addon): mixed
    {
        if (! $addon->is_addon) {
            throw new InvalidArgumentException(__('This plan is not an add-on.'));
        }

        $name = config('electrik.billing.subscription_name', 'electrik');
        $subscription = $team->subscription($name);

        if (! $subscription || ! ($subscription->active() || $subscription->onTrial() || $subscription->onGracePeriod())) {
            throw new InvalidArgumentException(__('Subscribe to a base plan before adding add-ons.'));
        }

        if ($subscription->hasPrice($addon->stripe_price_id)) {
            return $subscription;
        }

        $subscription->addPrice($addon->stripe_price_id);

        return $team->subscription($name);
    }
}
