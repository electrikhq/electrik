<?php

namespace Electrik\Support;

use Electrik\Models\Team;
use Electrik\Models\TeamActivityLog;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    /**
     * @param  array<string, mixed>  $properties
     */
    public static function log(
        Team $team,
        string $action,
        ?Authenticatable $actor = null,
        ?Model $subject = null,
        array $properties = [],
    ): TeamActivityLog {
        return TeamActivityLog::query()->create([
            'team_id' => $team->id,
            'user_id' => $actor?->getAuthIdentifier(),
            'action' => $action,
            'subject_type' => $subject ? $subject->getMorphClass() : null,
            'subject_id' => $subject?->getKey(),
            'properties' => $properties === [] ? null : $properties,
        ]);
    }
}
