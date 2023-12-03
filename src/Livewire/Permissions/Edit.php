<?php

namespace App\Livewire\Permissions;

use App\Models\Permission;
use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

class Edit extends Component {

	use WireToast;
	
	public Permission $permission;

	protected $rules = [
        'permission.name' => 'required|string|max:255',
        'permission.description' => 'nullable|string|max:255',
    ];

	public function mount(Permission $permission) {

        if(!auth()->user()->can('access-management.roles-permissions'))
            return redirect()->route('error.access-denied');
		$this->permission = $permission;
	}

	public function submit() {

		$this->validate();

	    $this->permission->save();

        toast()->success($this->permission->name.' permission has been been successfully updated')->push();
        return redirect()->route('permissions.index');
	}

    public function render() {
        return view('livewire.permissions.edit', ['permission' => $this->permission]);
    }
}
