<?php

namespace App\Actions\Teams;

use App\Models\Team;
use App\Models\User;
use App\Events\Team\TeamCreated;
use Illuminate\Support\Str;

class CreateTeam
{
    /**
     * Execute the action.
     *
     * @param  User  $owner
     * @param  string  $name
     * @return Team
     */
    public function execute(User $owner, string $name): Team
    {
        $team = Team::create([
            'owner_id' => $owner->id,
            'name' => $name,
            'slug' => Str::slug($name),
        ]);

        // Attach owner to team
        $team->users()->attach($owner->id, ['role' => 'owner']);

        // Fire event
        event(new TeamCreated($team));

        return $team;
    }
}
