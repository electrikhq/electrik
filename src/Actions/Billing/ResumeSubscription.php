<?php

namespace Electrik\Actions\Billing;

use Electrik\Models\Team;

class ResumeSubscription
{
    public function execute(Team $team, ?string $subscriptionName = null): void
    {
        $subscriptionName ??= config('electrik.billing.subscription_name', 'electrik');
        $subscription = $team->subscription($subscriptionName);

        if ($subscription?->onGracePeriod()) {
            $subscription->resume();
        }
    }
}
