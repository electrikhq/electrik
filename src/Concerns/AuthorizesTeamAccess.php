<?php

namespace Electrik\Concerns;

use Electrik\Models\Role;
use Electrik\Models\Team;
use Spatie\Permission\PermissionRegistrar;

trait AuthorizesTeamAccess
{
    /**
     * Ensure the user belongs to the team, Spatie can() is scoped to it,
     * and the session current team matches the page team (avoids cross-team privilege bugs).
     */
    protected function bindTeamContext(Team $team): void
    {
        $user = auth()->user();

        abort_unless($user && method_exists($user, 'teams') && $user->teams->contains($team->id), 403);

        if ((int) $user->current_team_id !== (int) $team->id) {
            $user->switchTeam($team);
            $user->refresh();
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);
    }

    protected function authorizeTeamPermission(Team $team, string $permission): void
    {
        $this->bindTeamContext($team);

        abort_unless(
            auth()->user()->can($permission) || auth()->user()->isOwnerOfTeam($team),
            403
        );
    }

    /**
     * @return list<string>
     */
    protected function assignableRoleNamesFor(Team $team): array
    {
        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);

        return Role::query()
            ->forTeam($team->id)
            ->where('name', '!=', 'owner')
            ->orderBy('name')
            ->pluck('name')
            ->map(fn ($name) => (string) $name)
            ->all();
    }
}
