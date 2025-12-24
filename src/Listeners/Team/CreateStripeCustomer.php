<?php

namespace App\Listeners\Team;

use App\Events\Team\TeamCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateStripeCustomer implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(TeamCreated $event): void
    {
        if (config('services.stripe.secret')) {
            $event->team->createOrGetStripeCustomer();
        }
    }
}

