<?php

namespace Electrik\Actions\Billing;

use Electrik\Models\Team;

class CancelSubscription
{
    /**
     * Cancel at period end (grace period) unless $immediately is true.
     */
    public function execute(Team $team, bool $immediately = false, ?string $subscriptionName = null): void
    {
        $subscriptionName ??= config('electrik.billing.subscription_name', 'electrik');
        $subscription = $team->subscription($subscriptionName);

        if (! $subscription) {
            return;
        }

        if ($immediately) {
            $subscription->cancelNow();

            return;
        }

        $subscription->cancel();
    }
}
