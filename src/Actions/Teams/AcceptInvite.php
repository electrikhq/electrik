<?php

namespace App\Actions\Teams;

use App\Models\Team;
use App\Models\TeamInvite;
use App\Models\User;
use App\Events\Team\MemberJoined;

class AcceptInvite
{
    /**
     * Execute the action.
     *
     * @param  TeamInvite  $invite
     * @param  User  $user
     * @return void
     */
    public function execute(TeamInvite $invite, User $user): void
    {
        // Attach user to team
        $invite->team->users()->attach($user->id, [
            'role' => $invite->role?->name ?? 'member',
        ]);

        // Assign role if specified
        if ($invite->role) {
            $user->assignRole($invite->role);
        }

        // Fire event
        event(new MemberJoined($invite->team, $user));

        // Delete the invite
        $invite->delete();
    }
}
