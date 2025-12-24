<?php

namespace App\Listeners\User;

use App\Notifications\WelcomeNotification;
use App\Events\User\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        $event->user->notify(new WelcomeNotification);
    }
}

