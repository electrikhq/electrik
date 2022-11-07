<?php

namespace App\Http\Livewire\Onboarding;

use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

class ChoosePlan extends Component {

	use WireToast;
	
    public function mount() {
		toast()->info('Choose a Plan')->push();
	}
	
    public function render() {

        return view('livewire.onboarding.choose-plan')->layout('layouts.livewire.onboarding');

    }
	
}
