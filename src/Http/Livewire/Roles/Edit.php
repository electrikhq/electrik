<?php

namespace Electrik\Http\Livewire\Roles;

use Electrik\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Electrik\Models\Role;
use Illuminate\Validation\Rule;
use Usernotnull\Toast\Concerns\WireToast;

class Edit extends Component {

	use WireToast;

	public Role $role;
	public $allPermissions;
	public $selectedPermissionsIds = [];
	public $selectedPermissions;

	function rules() {

		return [
			'role.name' => ['required', 'string', Rule::unique('roles', 'name')->ignore($this->role->id)->where(fn ($query) => $query->where('team_id', auth()->user()->currentTeam->id))],
			'role.description' => 'nullable|string|max:255',
			'role.display_name' => 'nullable|string|max:255',
		];
	}

	public function mount(Role $role) {
        // if(!auth()->user()->can('access-management.roles-permissions'))
        //     return redirect()->route('error.access-denied');

		$this->role = $role;

		$this->allPermissions = collect(Permission::all()->toArray())->groupBy('category_name');;

		$this->selectedPermissions = $this->role->permissions()->get()->sortby('name');

		/* it's necessecarty to prime ids as strvals */
		$this->selectedPermissionsIds = $this->role->permissions()->pluck('id')->map(
			function($id) {
				return strval($id);
			})->toArray();


		// $this->selectedPermissions = $this->role->permissions()->get()->sortby('name');

		// /* it's necessecarty to prime ids as strvals */
		// $this->selectedPermissionsIds = $this->role->permissions()->pluck('id')->map(
		// 	function($id) {
		// 		return strval($id);
		// 	})->toArray();
	}

	public function submit() {


		$this->validate();
		$this->role->save();
		$this->role->syncPermissions(
			$this->selectedPermissionsIds
		);

        toast()->success($this->role->display_name.' role has been been successfully updated')->pushOnNextPage();
        return redirect()->route('teams.roles.index');
	}

    public function render() {
        return view('electrik::livewire.roles.create-edit');
    }
}
