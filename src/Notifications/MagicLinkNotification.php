<?php

namespace Electrik\Notifications;

use Electrik\Support\MagicLink;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MagicLinkNotification extends Notification
{
    use Queueable;

    public function __construct(public string $url) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Your sign-in link'))
            ->greeting(__('Hello!'))
            ->line(__('Click the link below to sign in. It expires in :minutes minutes.', [
                'minutes' => MagicLink::expireMinutes(),
            ]))
            ->action(__('Sign in'), $this->url)
            ->line(__('If you did not request this, you can ignore this email.'));
    }
}
