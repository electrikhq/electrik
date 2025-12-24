<?php

namespace App\Actions\Teams;

use App\Models\Team;
use App\Models\TeamInvite;
use App\Models\Role;
use App\Events\Team\MemberInvited;
use Illuminate\Support\Str;

class InviteMember
{
    /**
     * Execute the action.
     *
     * @param  Team  $team
     * @param  string  $email
     * @param  Role|null  $role
     * @return TeamInvite
     */
    public function execute(Team $team, string $email, ?Role $role = null): TeamInvite
    {
        $invite = TeamInvite::create([
            'team_id' => $team->id,
            'email' => $email,
            'token' => Str::random(40),
            'role_id' => $role?->id,
            'expires_at' => now()->addDays(7),
        ]);

        // Fire event
        event(new MemberInvited($invite));

        return $invite;
    }
}
