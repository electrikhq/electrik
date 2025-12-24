<?php

namespace App\Events\Billing;

use App\Models\Team;
use Laravel\Cashier\Subscription;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionCreated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Team $team,
        public Subscription $subscription
    ) {
        //
    }
}

