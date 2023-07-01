<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

class TwoFactor extends Component {
	
	use WireToast;

	public function render() {
        return view('livewire.auth.two-factor')->layout('layouts.livewire.guest');
    }

}
