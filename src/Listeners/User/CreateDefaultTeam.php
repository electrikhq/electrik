<?php

namespace App\Listeners\User;

use App\Models\Team;
use App\Actions\Teams\CreateTeam;
use App\Events\User\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;

class CreateDefaultTeam implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        $teamName = $event->user->name . "'s Team";
        
        $createTeamAction = new CreateTeam();
        $team = $createTeamAction->execute($event->user, $teamName);

        // Set as current team
        $event->user->update(['current_team_id' => $team->id]);
    }
}
