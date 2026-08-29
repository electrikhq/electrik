<?php

namespace Electrik\Listeners;

use Electrik\Support\ActivityLogger;
use Lab404\Impersonate\Events\LeaveImpersonation;
use Lab404\Impersonate\Events\TakeImpersonation;

class LogImpersonationActivity
{
    public function handleTake(TakeImpersonation $event): void
    {
        $team = $this->teamFor($event->impersonator) ?? $this->teamFor($event->impersonated);

        if (! $team) {
            return;
        }

        ActivityLogger::log($team, 'member.impersonation_started', $event->impersonator, $event->impersonated, [
            'name' => $event->impersonated->name ?? (string) $event->impersonated->getAuthIdentifier(),
            'actor_name' => $event->impersonator->name ?? (string) $event->impersonator->getAuthIdentifier(),
        ]);
    }

    public function handleLeave(LeaveImpersonation $event): void
    {
        $team = $this->teamFor($event->impersonator) ?? $this->teamFor($event->impersonated);

        if (! $team) {
            return;
        }

        ActivityLogger::log($team, 'member.impersonation_stopped', $event->impersonator, $event->impersonated, [
            'name' => $event->impersonated->name ?? (string) $event->impersonated->getAuthIdentifier(),
        ]);
    }

    protected function teamFor(mixed $user): mixed
    {
        if (! is_object($user) || ! method_exists($user, 'currentTeam')) {
            return null;
        }

        return $user->currentTeam;
    }
}
