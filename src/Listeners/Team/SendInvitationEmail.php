<?php

namespace App\Listeners\Team;

use App\Notifications\InvitedToTeamNotification;
use App\Events\Team\MemberInvited;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendInvitationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(MemberInvited $event): void
    {
        Notification::route('mail', $event->invite->email)
            ->notify(new InvitedToTeamNotification($event->invite));
    }
}

