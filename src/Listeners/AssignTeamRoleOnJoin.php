<?php

namespace Electrik\Listeners;

use Electrik\Actions\Billing\SyncTeamSeats;
use Electrik\Models\Role;
use Electrik\Models\Team;
use Electrik\Support\ActivityLogger;
use Electrik\Support\EnsuresTeamRoles;
use Electrik\Support\TeamInviteContext;
use Mpociot\Teamwork\Events\UserJoinedTeam;
use Spatie\Permission\PermissionRegistrar;

class AssignTeamRoleOnJoin
{
    public function __construct(protected EnsuresTeamRoles $ensures) {}

    public function handle(UserJoinedTeam $event): void
    {
        $user = $event->getUser();
        $teamId = $event->getTeamId();

        if (! $user || ! $teamId || ! method_exists($user, 'syncRoles')) {
            return;
        }

        $this->ensures->syncCatalog();
        $this->ensures->ensureForTeam($teamId);

        app(PermissionRegistrar::class)->setPermissionsTeamId($teamId);

        $team = Team::query()->find($teamId);
        $inviteRole = TeamInviteContext::pullPendingRole();

        $assignable = Role::query()
            ->forTeam($teamId)
            ->where('name', '!=', 'owner')
            ->pluck('name')
            ->all();

        if (is_string($inviteRole) && in_array($inviteRole, $assignable, true)) {
            $role = $inviteRole;
        } elseif ($team && (int) $team->owner_id === (int) $user->getAuthIdentifier()) {
            $role = 'owner';
        } else {
            $role = 'member';
        }

        $user->syncRoles([$role]);

        if ($team) {
            ActivityLogger::log($team, 'member.joined', $user, $user, ['name' => $user->name]);
            app(SyncTeamSeats::class)->execute($team);
        }
    }
}
