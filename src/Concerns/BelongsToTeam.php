<?php

namespace Electrik\Concerns;

use Electrik\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Scope Eloquent models to the authenticated user's current team.
 *
 * Usage: `use BelongsToTeam;` on app models (generators add this with --team).
 */
trait BelongsToTeam
{
    public static function bootBelongsToTeam(): void
    {
        static::creating(function (Model $model): void {
            if (! empty($model->team_id)) {
                return;
            }

            $teamId = static::currentTeamId();

            if ($teamId !== null) {
                $model->team_id = $teamId;
            }
        });

        static::addGlobalScope('team', function (Builder $builder): void {
            $teamId = static::currentTeamId();

            if ($teamId === null) {
                return;
            }

            $builder->where(
                $builder->getModel()->getTable().'.team_id',
                $teamId
            );
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public static function currentTeamId(): ?int
    {
        $user = auth()->user();

        if (! $user || ! $user->currentTeam) {
            return null;
        }

        return (int) $user->currentTeam->getKey();
    }

    /**
     * Query without the current-team global scope.
     */
    public static function withoutTeamScope(): Builder
    {
        return static::withoutGlobalScope('team');
    }
}
