<?php

namespace App\Actions\Teams;

use App\Models\Team;
use App\Models\User;
use App\Events\Team\TeamSwitched;

class SwitchTeam
{
    /**
     * Execute the action.
     *
     * @param  User  $user
     * @param  Team  $team
     * @return void
     * @throws \Exception
     */
    public function execute(User $user, Team $team): void
    {
        if (!$user->teams()->where('teams.id', $team->id)->exists()) {
            throw new \Exception('User does not belong to this team.');
        }

        $user->update(['current_team_id' => $team->id]);

        // Fire event
        event(new TeamSwitched($user, $team));
    }
}
