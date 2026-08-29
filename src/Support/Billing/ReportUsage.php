<?php

namespace Electrik\Support\Billing;

use Electrik\Models\StripePlan;
use Electrik\Models\Team;
use InvalidArgumentException;

class ReportUsage
{
    /**
     * Report metered usage against the team's active subscription item for a price.
     */
    public function execute(Team $team, string $stripePriceId, int $quantity = 1, ?\DateTimeInterface $timestamp = null): void
    {
        $name = config('electrik.billing.subscription_name', 'electrik');
        $subscription = $team->subscription($name);

        if (! $subscription || ! $subscription->valid()) {
            throw new InvalidArgumentException(__('No active subscription to report usage against.'));
        }

        $item = $subscription->items->firstWhere('stripe_price', $stripePriceId)
            ?? $subscription->items->first();

        if (! $item) {
            throw new InvalidArgumentException(__('No subscription item found for metered usage.'));
        }

        $item->reportUsage($quantity, $timestamp);
    }

    public function executeForPlan(Team $team, StripePlan $plan, int $quantity = 1): void
    {
        if (! $plan->metered) {
            throw new InvalidArgumentException(__('This plan is not metered.'));
        }

        $this->execute($team, $plan->stripe_price_id, $quantity);
    }
}
