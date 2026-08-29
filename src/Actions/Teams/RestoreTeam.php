<?php

namespace Electrik\Actions\Teams;

use Electrik\Models\Team;
use Electrik\Support\ActivityLogger;
use Electrik\Support\TeamWebhookDispatcher;
use Illuminate\Contracts\Auth\Authenticatable;

class RestoreTeam
{
    public function execute(Team $team, Authenticatable $actor): void
    {
        $team->forceFill(['archived_at' => null])->save();

        ActivityLogger::log($team, 'team.restored', $actor, $team);
        TeamWebhookDispatcher::dispatch($team, 'team.restored', [
            'team_id' => $team->id,
            'name' => $team->name,
        ]);
    }
}
