<?php

namespace Electrik\Livewire\Teams\Roles;

use Electrik\Concerns\AuthorizesTeamAccess;
use Electrik\Models\Permission;
use Electrik\Models\Role;
use Electrik\Models\Team;
use Electrik\Support\PlanFeatures;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\PermissionRegistrar;

#[Layout('electrik::components.layouts.app')]
#[Title('Create role')]
class Create extends Component
{
    use AuthorizesTeamAccess;

    #[Locked]
    public Team $team;

    public string $name = '';

    public string $display_name = '';

    /** @var array<int, string> */
    public array $selectedPermissions = [];

    public function mount(Team $team): void
    {
        $this->authorizeTeamPermission($team, 'access.roles');
        abort_unless(PlanFeatures::has($team, 'custom_roles'), 403);
        $this->team = $team;
    }

    public function save(): void
    {
        $this->authorizeTeamPermission($this->team, 'access.roles');

        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::notIn(config('electrik.teams.roles', [])),
                Rule::unique('roles', 'name')->where(fn ($q) => $q->where('team_id', $this->team->id)),
            ],
            'display_name' => ['nullable', 'string', 'max:255'],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['string'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'] ?: ucfirst($validated['name']),
            'guard_name' => config('auth.defaults.guard', 'web'),
            'team_id' => $this->team->id,
        ]);

        app(PermissionRegistrar::class)->setPermissionsTeamId($this->team->id);
        $role->syncPermissions($validated['selectedPermissions'] ?? []);

        session()->flash('status', __('Role created.'));

        $this->redirect(route('teams.roles.index', $this->team), navigate: true);
    }

    public function render()
    {
        return view('electrik::livewire.teams.roles.form', [
            'permissions' => Permission::query()->orderBy('category_name')->orderBy('name')->get()->groupBy('category_name'),
            'editing' => false,
            'locked' => false,
        ]);
    }
}
