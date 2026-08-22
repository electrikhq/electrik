<?php

namespace Electrik\Listeners;

use Electrik\Support\EnsuresTeamRoles;
use Electrik\Support\TeamInviteContext;
use Illuminate\Auth\Events\Registered;

class CreateDefaultTeam
{
    public function __construct(protected EnsuresTeamRoles $ensures) {}

    public function handle(Registered $event): void
    {
        // Invitees joining an existing team should not get a personal default workspace.
        if (TeamInviteContext::peekToken()) {
            return;
        }

        $user = $event->user;

        if (! method_exists($user, 'createOwnedTeam')) {
            return;
        }

        if ($user->teams()->exists()) {
            return;
        }

        $name = trim((string) ($user->name ?? 'My'))."'s Team";

        $team = $user->createOwnedTeam(['name' => $name], true);

        $this->ensures->syncCatalog();
        if ($team) {
            $this->ensures->ensureForTeam($team);
        }
    }
}
