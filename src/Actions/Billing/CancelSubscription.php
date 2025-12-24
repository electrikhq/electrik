<?php

namespace App\Actions\Billing;

use App\Models\Team;
use App\Events\Billing\SubscriptionCanceled;
use Laravel\Cashier\Subscription;

class CancelSubscription
{
    /**
     * Execute the action.
     *
     * @param  Team  $team
     * @param  string|null  $subscriptionName
     * @return void
     */
    public function execute(Team $team, ?string $subscriptionName = null): void
    {
        $subscriptionName = $subscriptionName ?? config('electrik.default_subscription_name', 'electrik');
        
        $subscription = $team->subscription($subscriptionName);
        
        if ($subscription) {
            $subscription->cancelNow();
            
            // Fire event
            event(new SubscriptionCanceled($team, $subscription));
        }
    }
}
