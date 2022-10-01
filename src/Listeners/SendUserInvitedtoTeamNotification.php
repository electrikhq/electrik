<?php

namespace App\Listeners;

use App\Events\Mpociot\Teamwork\Events\UserInvitedToTeam;
use App\Notifications\InvitedToTeamNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use Mpociot\Teamwork\Events\UserInvitedToTeam as EventsUserInvitedToTeam;

class SendUserInvitedtoTeamNotification implements ShouldQueue {

	use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\Mpociot\Teamwork\Events\UserInvitedToTeam  $event
     * @return void
     */
    public function handle(EventsUserInvitedToTeam $event) {
        
        $invitation = $event->getInvite();
        // $teamId = $event->getTeamId();

		Notification::route('mail', $invitation->email)
            // ->route('nexmo', '5555555555')
            // ->route('slack', 'https://hooks.slack.com/services/...')
            ->notify(new InvitedToTeamNotification($invitation));

        // Do something with the user and team ID.
    }
}
