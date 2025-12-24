<?php

namespace App\Listeners\Billing;

use App\Events\Billing\SubscriptionCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SyncStripeCustomer implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(SubscriptionCreated $event): void
    {
        if ($event->team->hasStripeId()) {
            $event->team->syncStripeCustomerDetails();
        }
    }
}

