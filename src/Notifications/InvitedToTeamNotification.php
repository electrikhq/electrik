<?php

namespace App\Notifications;

use App\Models\TeamInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitedToTeamNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public TeamInvite $invite
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $acceptUrl = route('teams.invite.accept', ['token' => $this->invite->token]);

        return (new MailMessage)
            ->subject('Invitation to join ' . $this->invite->team->name)
            ->greeting('Hello!')
            ->line('You have been invited to join the team: ' . $this->invite->team->name)
            ->action('Accept Invitation', $acceptUrl)
            ->line('If you did not expect this invitation, you can safely ignore this email.');
    }
}

