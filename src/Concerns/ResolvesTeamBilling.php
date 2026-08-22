<?php

namespace Electrik\Concerns;

use Electrik\Models\StripePlan;
use Electrik\Models\Team;
use Laravel\Cashier\Subscription;

trait ResolvesTeamBilling
{
    protected function currentTeamOrRedirect(): ?Team
    {
        $team = auth()->user()?->currentTeam;

        if (! $team) {
            $this->redirect(route('teams.index'), navigate: true);

            return null;
        }

        return $team;
    }

    protected function teamSubscription(?Team $team = null): ?Subscription
    {
        $team ??= auth()->user()?->currentTeam;

        if (! $team) {
            return null;
        }

        return $team->subscription(config('electrik.billing.subscription_name', 'electrik'));
    }

    protected function planForSubscription(?Subscription $subscription): ?StripePlan
    {
        if (! $subscription) {
            return null;
        }

        $priceId = $subscription->stripe_price
            ?? $subscription->items()->first()?->stripe_price;

        if (! $priceId) {
            return null;
        }

        $planModel = config('electrik.billing.plan_model', StripePlan::class);

        return $planModel::query()->where('stripe_price_id', $priceId)->first();
    }
}
