<?php

namespace App\Http\Livewire\Teams\Members;

use App\Models\Role;
use App\Models\User;
use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

class Edit extends Component {

	use WireToast;

	public $selectedRoleId;

	public $user;
	
	function rules() {
		return [
			'selectedRoleId' => 'required',
		];
	}
	public function mount(User $user) {
		$this->user = $user;
		$this->selectedRoleId = ($this->user->roles()->count()) ? $this->user->roles()->first()->id : null; 
	}

	public function update() {
		$this->validate();
		$this->user->assignRole($this->selectedRoleId);
		toast()->success('User updates successfully')->push();
	}

    public function render() {
        return view('livewire.teams.members.edit', [
			'allRoles' => Role::all(),
		]);
    }
}
