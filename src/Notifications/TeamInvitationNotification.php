<?php

namespace Electrik\Notifications;

use Electrik\Support\TeamInviteContext;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Mpociot\Teamwork\TeamInvite;

class TeamInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(public TeamInvite $invite) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $team = $this->invite->team->name;
        $role = $this->invite->role ? ucfirst($this->invite->role) : 'Member';
        $inviter = $this->invite->inviter?->name ?? __('A teammate');
        $expires = $this->invite->expires_at
            ? \Illuminate\Support\Carbon::parse($this->invite->expires_at)->toFormattedDateString()
            : null;

        $mail = (new MailMessage)
            ->subject(__('Invitation to join :team', ['team' => $team]))
            ->greeting(__('Hello!'))
            ->line(__(':inviter invited you to join :team as :role.', [
                'inviter' => $inviter,
                'team' => $team,
                'role' => $role,
            ]));

        if ($expires) {
            $mail->line(__('This invitation expires on :date.', ['date' => $expires]));
        }

        return $mail
            ->action(__('Accept invitation'), TeamInviteContext::acceptUrl($this->invite))
            ->line(__('Or decline: :url', ['url' => TeamInviteContext::denyUrl($this->invite)]));
    }
}
