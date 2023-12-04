<?php

namespace App\Livewire\Permissions;

use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

class Index extends Component {
	
	use WireToast;

    public function mount()
    {
        if(!auth()->user()->can('access-management.roles-permissions'))
            return redirect()->route('error.access-denied');
    }

    public function render()
    {
        return view('livewire.permissions.index');
    }
}
