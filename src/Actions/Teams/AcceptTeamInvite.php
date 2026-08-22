<?php

namespace Electrik\Actions\Teams;

use Electrik\Models\Role;
use Electrik\Support\TeamInviteContext;
use Illuminate\Contracts\Auth\Authenticatable;
use Mpociot\Teamwork\TeamInvite;
use RuntimeException;
use Spatie\Permission\PermissionRegistrar;

class AcceptTeamInvite
{
    /**
     * Accept a team invite for the given user.
     *
     * @throws RuntimeException
     */
    public function execute(Authenticatable $user, TeamInvite $invite): void
    {
        if (TeamInviteContext::isExpired($invite)) {
            throw new RuntimeException(__('This invitation has expired.'));
        }

        if (strcasecmp((string) $user->email, (string) $invite->email) !== 0) {
            throw new RuntimeException(__('This invitation was sent to a different email address.'));
        }

        $team = $invite->team;
        $teamId = $invite->team_id;
        $role = is_string($invite->role) && $invite->role !== '' ? $invite->role : 'member';

        if ($user->teams()->whereKey($teamId)->exists()) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($teamId);

            $assignable = Role::query()
                ->forTeam($teamId)
                ->where('name', '!=', 'owner')
                ->pluck('name')
                ->all();

            if (method_exists($user, 'syncRoles') && in_array($role, $assignable, true)) {
                $user->syncRoles([$role]);
            }

            $invite->delete();
            $user->switchTeam($teamId);

            return;
        }

        TeamInviteContext::setPendingRole($role);

        $user->attachTeam($team);
        $invite->delete();
        $user->switchTeam($teamId);

        TeamInviteContext::pullPendingRole();
    }
}
