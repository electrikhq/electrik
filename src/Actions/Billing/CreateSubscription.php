<?php

namespace App\Actions\Billing;

use App\Models\Team;
use App\Models\StripePlan;
use App\Events\Billing\SubscriptionCreated;
use Laravel\Cashier\Subscription;

class CreateSubscription
{
    /**
     * Execute the action.
     *
     * @param  Team  $team
     * @param  StripePlan  $plan
     * @param  string|null  $paymentMethod
     * @return Subscription
     */
    public function execute(Team $team, StripePlan $plan, ?string $paymentMethod = null): Subscription
    {
        $subscriptionName = config('electrik.default_subscription_name', 'electrik');

        $subscription = $team->newSubscription($subscriptionName, $plan->stripe_price_id);

        if ($plan->isFree()) {
            // Free plan - no payment method needed (unless configured)
            if (config('electrik.cc_required_for_free_plan', false) && $paymentMethod) {
                $subscription->create($paymentMethod);
            } else {
                $subscription->create();
            }
        } else {
            // Paid plan - payment method required
            if (!$paymentMethod) {
                throw new \Exception('Payment method is required for paid plans.');
            }
            $subscription->create($paymentMethod);
        }

        $subscription = $team->subscription($subscriptionName);

        // Fire event
        event(new SubscriptionCreated($team, $subscription));

        return $subscription;
    }
}

