<?php

namespace Electrik\Livewire\Teams;

use Electrik\Actions\Billing\SyncTeamSeats;
use Electrik\Concerns\AuthorizesTeamAccess;
use Electrik\Support\ActivityLogger;
use Electrik\Models\Role;
use Electrik\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\PermissionRegistrar;

#[Layout('electrik::components.layouts.app')]
#[Title('Team members')]
class Members extends Component
{
    use AuthorizesTeamAccess;

    #[Locked]
    public Team $team;

    public function mount(Team $team): void
    {
        $this->bindTeamContext($team);
        $this->team = $team;
    }

    public function remove(int $userId): void
    {
        $this->authorizeTeamPermission($this->team, 'teams.members');
        abort_if((int) $this->team->owner_id === $userId, 422);

        $member = $this->team->users()->whereKey($userId)->firstOrFail();

        app(PermissionRegistrar::class)->setPermissionsTeamId($this->team->id);
        if (method_exists($member, 'syncRoles')) {
            $member->syncRoles([]);
        }

        $member->detachTeam($this->team);

        ActivityLogger::log($this->team, 'member.removed', auth()->user(), $member, [
            'name' => $member->name,
        ]);
        app(SyncTeamSeats::class)->execute($this->team);
    }

    public function leave(): void
    {
        $this->bindTeamContext($this->team);
        $user = auth()->user();

        if ((int) $this->team->owner_id === (int) $user->getAuthIdentifier()) {
            $this->addError('leave', __('Transfer ownership before leaving, or delete the team.'));

            return;
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($this->team->id);
        if (method_exists($user, 'syncRoles')) {
            $user->syncRoles([]);
        }

        $user->detachTeam($this->team);

        ActivityLogger::log($this->team, 'member.left', $user, $user, ['name' => $user->name]);
        app(SyncTeamSeats::class)->execute($this->team);

        if ((int) $user->current_team_id === (int) $this->team->id) {
            $next = $user->teams()->first();
            if ($next) {
                $user->switchTeam($next);
                app(PermissionRegistrar::class)->setPermissionsTeamId($next->id);
            } else {
                $user->current_team_id = null;
                $user->save();
            }
        }

        $this->redirect(
            $user->teams()->exists() ? route('teams.index') : route('teams.create'),
            navigate: true
        );
    }

    public function updateRole(int $userId, string $role): void
    {
        $this->authorizeTeamPermission($this->team, 'teams.members');
        abort_if((int) $this->team->owner_id === $userId, 422);

        $assignable = $this->assignableRoleNamesFor($this->team);
        abort_unless(in_array($role, $assignable, true), 422);

        app(PermissionRegistrar::class)->setPermissionsTeamId($this->team->id);

        $member = $this->team->users()->whereKey($userId)->firstOrFail();
        $member->syncRoles([$role]);

        ActivityLogger::log(
            $this->team,
            'member.role_changed',
            auth()->user(),
            $member,
            properties: ['name' => $member->name, 'role' => $role]
        );
    }

    public function cancelInvite(int $inviteId): void
    {
        $this->authorizeTeamPermission($this->team, 'teams.invite');

        $invite = $this->team->invites()->whereKey($inviteId)->firstOrFail();
        $invite->delete();

        session()->flash('status', __('Invitation cancelled.'));
    }

    public function resendInvite(int $inviteId): void
    {
        $this->authorizeTeamPermission($this->team, 'teams.invite');

        $invite = $this->team->invites()->whereKey($inviteId)->firstOrFail();

        $days = max(1, (int) config('electrik.teams.invite_expires_days', 7));
        $invite->accept_token = \Illuminate\Support\Str::random(40);
        $invite->deny_token = \Illuminate\Support\Str::random(40);
        if (\Illuminate\Support\Facades\Schema::hasColumn($invite->getTable(), 'expires_at')) {
            $invite->expires_at = now()->addDays($days);
        }
        $invite->save();

        \Illuminate\Support\Facades\Notification::route('mail', $invite->email)
            ->notify(new \Electrik\Notifications\TeamInvitationNotification($invite));

        session()->flash('status', __('Invitation resent.'));
    }

    public function render()
    {
        $this->bindTeamContext($this->team);

        $members = $this->team->users()->orderBy('name')->get()->map(function ($member) {
            $member->electrik_role = $member->roles->first()?->name;

            return $member;
        });

        return view('electrik::livewire.teams.members', [
            'members' => $members,
            'invitations' => $this->team->invites()->orderByDesc('created_at')->get(),
            'assignableRoles' => $this->assignableRoleNamesFor($this->team),
            'canManageMembers' => auth()->user()->can('teams.members') || auth()->user()->isOwnerOfTeam($this->team),
        ]);
    }
}
