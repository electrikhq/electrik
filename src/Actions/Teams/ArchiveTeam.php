<?php

namespace Electrik\Actions\Teams;

use Electrik\Models\Team;
use Electrik\Support\ActivityLogger;
use Electrik\Support\TeamWebhookDispatcher;
use Illuminate\Contracts\Auth\Authenticatable;

class ArchiveTeam
{
    public function execute(Team $team, Authenticatable $actor): void
    {
        $team->forceFill(['archived_at' => now()])->save();

        ActivityLogger::log($team, 'team.archived', $actor, $team);
        TeamWebhookDispatcher::dispatch($team, 'team.archived', [
            'team_id' => $team->id,
            'name' => $team->name,
        ]);
    }
}
