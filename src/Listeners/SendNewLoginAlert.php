<?php

namespace Electrik\Listeners;

use Electrik\Notifications\NewLoginAlertNotification;
use Electrik\Support\Auth as ElectrikAuth;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Schema;

class SendNewLoginAlert
{
    public function handle(Login $event): void
    {
        if (! config('electrik.auth.login_alerts', true)) {
            return;
        }

        $user = $event->user;

        if (! is_object($user) || ! method_exists($user, 'notify')) {
            return;
        }

        if (ElectrikAuth::isSuspended($user)) {
            return;
        }

        $ip = request()->ip();
        $agent = (string) request()->userAgent();

        if ($this->isFamiliarLogin($user, $ip)) {
            return;
        }

        $user->notify(new NewLoginAlertNotification(
            ip: $ip,
            userAgent: $agent,
            at: now(),
        ));
    }

    protected function isFamiliarLogin(object $user, ?string $ip): bool
    {
        if (blank($ip) || ! Schema::hasTable(config('authentication-log.table_name', 'authentication_log'))) {
            return false;
        }

        if (! method_exists($user, 'authentications')) {
            return false;
        }

        return $user->authentications()
            ->where('login_successful', true)
            ->where('ip_address', $ip)
            ->where('login_at', '<', now()->subMinute())
            ->exists();
    }
}
