<?php

namespace Electrik\Livewire\Teams\Permissions;

use Electrik\Concerns\AuthorizesTeamAccess;
use Electrik\Models\Permission;
use Electrik\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Permissions')]
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

    public function render()
    {
        return view('electrik::livewire.teams.permissions.index', [
            'permissions' => Permission::query()->orderBy('category_name')->orderBy('name')->get()->groupBy('category_name'),
        ]);
    }
}
