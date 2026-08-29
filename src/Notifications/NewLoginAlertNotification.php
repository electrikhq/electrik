<?php

namespace Electrik\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class NewLoginAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ?string $ip,
        public string $userAgent,
        public Carbon $at,
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('New sign-in to your account'))
            ->line(__('We noticed a sign-in from a new IP or device.'))
            ->line(__('IP: :ip', ['ip' => $this->ip ?: __('Unknown')]))
            ->line(__('Device: :device', ['device' => \Illuminate\Support\Str::limit($this->userAgent, 120)]))
            ->line(__('Time: :time', ['time' => $this->at->toDayDateTimeString()]))
            ->action(__('Review sessions'), url(route('settings.sessions', absolute: false)));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'login_alert',
            'ip' => $this->ip,
            'user_agent' => $this->userAgent,
            'at' => $this->at->toIso8601String(),
            'message' => __('New sign-in from :ip', ['ip' => $this->ip ?: __('Unknown')]),
        ];
    }
}
