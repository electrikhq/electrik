<?php

namespace Electrik\Livewire\Teams\Roles;

use Electrik\Concerns\AuthorizesTeamAccess;
use Electrik\Models\Role;
use Electrik\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\PermissionRegistrar;

#[Layout('electrik::components.layouts.app')]
#[Title('Roles')]
class Index extends Component
{
    use AuthorizesTeamAccess;

    #[Locked]
    public Team $team;

    public function mount(Team $team): void
    {
        $this->authorizeTeamPermission($team, 'access.roles');
        $this->team = $team;
    }

    public function delete(int $roleId): void
    {
        $this->authorizeTeamPermission($this->team, 'access.roles');

        $role = Role::query()->forTeam($this->team->id)->findOrFail($roleId);
        abort_if($role->isSystem(), 422);

        app(PermissionRegistrar::class)->setPermissionsTeamId($this->team->id);

        if ($role->users()->count() > 0) {
            session()->flash('error', __('Reassign members before deleting this role.'));

            return;
        }

        $role->delete();
        session()->flash('status', __('Role deleted.'));
    }

    public function render()
    {
        $this->bindTeamContext($this->team);

        return view('electrik::livewire.teams.roles.index', [
            'roles' => Role::query()->forTeam($this->team->id)->orderBy('name')->get(),
        ]);
    }
}
