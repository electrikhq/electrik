<?php

namespace Electrik\Actions\Billing;

use Electrik\Models\Team;
use Electrik\Support\PlanFeatures;

class SyncTeamSeats
{
    /**
     * Sync Stripe subscription quantity to team member count when seat billing is enabled.
     */
    public function execute(Team $team): void
    {
        $plan = PlanFeatures::planForTeam($team);

        if (! $plan?->seat_billing) {
            return;
        }

        $subscription = $team->subscription(config('electrik.billing.subscription_name', 'electrik'));

        if (! $subscription || ! ($subscription->active() || $subscription->onTrial())) {
            return;
        }

        $quantity = max(1, $team->users()->count());

        if ((int) $subscription->quantity === $quantity) {
            return;
        }

        $subscription->updateQuantity($quantity);
    }
}
