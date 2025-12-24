<?php

namespace App\Listeners\Billing;

use App\Notifications\SubscriptionCreatedNotification;
use App\Events\Billing\SubscriptionCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendConfirmationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(SubscriptionCreated $event): void
    {
        $event->team->owner->notify(new SubscriptionCreatedNotification($event->subscription));
    }
}

