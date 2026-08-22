<?php

namespace Electrik\Livewire\Teams\Roles;

use Electrik\Concerns\AuthorizesTeamAccess;
use Electrik\Models\Permission;
use Electrik\Models\Role;
use Electrik\Models\Team;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\PermissionRegistrar;

#[Layout('electrik::components.layouts.app')]
#[Title('Edit role')]
class Edit extends Component
{
    use AuthorizesTeamAccess;

    #[Locked]
    public Team $team;

    #[Locked]
    public Role $role;

    public string $name = '';

    public string $display_name = '';

    /** @var array<int, string> */
    public array $selectedPermissions = [];

    public function mount(Team $team, Role $role): void
    {
        $this->authorizeTeamPermission($team, 'access.roles');
        abort_unless((int) $role->team_id === (int) $team->id, 404);

        $this->team = $team;
        $this->role = $role;
        $this->name = $role->name;
        $this->display_name = (string) ($role->getRawOriginal('display_name') ?: $role->display_name);
        $this->selectedPermissions = $role->permissions()->pluck('name')->map(fn ($n) => (string) $n)->all();
    }

    public function save(): void
    {
        $this->authorizeTeamPermission($this->team, 'access.roles');
        abort_unless((int) $this->role->team_id === (int) $this->team->id, 404);

        if ($this->role->name === 'owner') {
            session()->flash('error', __('The owner role always has full access.'));

            return;
        }

        $rules = [
            'display_name' => ['nullable', 'string', 'max:255'],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['string'],
        ];

        if (! $this->role->isSystem()) {
            $rules['name'] = [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::notIn(config('electrik.teams.roles', [])),
                Rule::unique('roles', 'name')
                    ->ignore($this->role->id)
                    ->where(fn ($q) => $q->where('team_id', $this->team->id)),
            ];
        }

        $validated = $this->validate($rules);

        if (! $this->role->isSystem()) {
            $this->role->name = $validated['name'];
        }

        $this->role->display_name = $validated['display_name'] ?: ucfirst($this->role->name);
        $this->role->save();

        app(PermissionRegistrar::class)->setPermissionsTeamId($this->team->id);
        $this->role->syncPermissions($validated['selectedPermissions'] ?? []);

        session()->flash('status', __('Role updated.'));

        $this->redirect(route('teams.roles.index', $this->team), navigate: true);
    }

    public function render()
    {
        return view('electrik::livewire.teams.roles.form', [
            'permissions' => Permission::query()->orderBy('category_name')->orderBy('name')->get()->groupBy('category_name'),
            'editing' => true,
            'locked' => $this->role->name === 'owner',
            'system' => $this->role->isSystem(),
        ]);
    }
}
