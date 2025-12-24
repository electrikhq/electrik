<?php

namespace App\Listeners\Billing;

use App\Notifications\PaymentFailedNotification;
use App\Events\Billing\PaymentFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandlePaymentFailure implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PaymentFailed $event): void
    {
        $event->team->owner->notify(new PaymentFailedNotification($event->paymentData));
    }
}

