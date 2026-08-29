<?php

namespace Electrik\Support;

use Electrik\Models\Activity;
use Electrik\Models\Team;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    /**
     * Thin wrapper around spatie/laravel-activitylog scoped to a team.
     *
     * @param  array<string, mixed>  $properties
     */
    public static function log(
        Team $team,
        string $action,
        ?Authenticatable $actor = null,
        ?Model $subject = null,
        array $properties = [],
    ): ?Activity {
        $logger = activity('team')
            ->performedOn($subject ?? $team)
            ->event($action)
            ->withProperties($properties)
            ->tap(function (Activity $activity) use ($team): void {
                $activity->team_id = $team->getKey();
            });

        if ($actor instanceof Model) {
            $logger->causedBy($actor);
        } else {
            $logger->causedByAnonymous();
        }

        /** @var Activity|null $activity */
        $activity = $logger->log($action);

        return $activity;
    }
}
