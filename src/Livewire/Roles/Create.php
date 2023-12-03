<?php

namespace App\Livewire\Roles;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use App\Models\Role;
use Illuminate\Validation\Rule;
use Usernotnull\Toast\Concerns\WireToast;

class Create extends Component {

	use WireToast;

	protected $team;
	public Role $role;
	public $allPermissions;
	public $selectedPermissionsIds = [];
	public $selectedPermissions;

	function rules() {

		return [
			// 'role.name' => ['required','string', 'unique:roles,name,NULL,team_id'.auth()->user()->currentTeam->id],
			'role.name' => ['required','string', Rule::unique('roles', 'name')->where(fn ($query) => $query->where('team_id', auth()->user()->currentTeam->id))],
			'role.description' => 'nullable|string|max:255',
		];

	}

	public function mount() {
        // if(!auth()->user()->can('access-management.roles-permissions'))
            // return redirect()->route('error.access-denied');

		$this->team = auth()->user()->currentTeam;

		$this->role = new Role;
	
		$this->allPermissions = collect(Permission::all()->toArray())->groupBy('category_name');;

		$this->selectedPermissions = $this->role->permissions()->get()->sortby('name');

		/* it's necessecarty to prime ids as strvals */
		$this->selectedPermissionsIds = $this->role->permissions()->pluck('id')->map(
			function($id) {
				return strval($id);
			})->toArray();

		// $this->selectedPermissions = $this->role->permissions()->get()->sortby('name');

		/* it's necessecarty to prime ids as strvals */
		// $this->selectedPermissionsIds = $this->role->permissions()->pluck('id')->map(
		// 	function($id) {
		// 		return strval($id);
		// 	})->toArray();

	}

	public function submit() {

		$this->validate();
		$this->role->team_id  = auth()->user()->currentTeam->id;
		// dd($role);
		$this->role->save();
		$this->role->syncPermissions(
			$this->selectedPermissionsIds
		);

        toast()->success($this->role->display_name.' role has been been successfully updated')->pushOnNextPage();
        return redirect()->route('teams.roles.index');
	}

    public function render() {
        return view('livewire.roles.create-edit');
    }
}
