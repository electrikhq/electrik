<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class Personal extends Component
{

	public $user;
	
	protected function rules() {
		return [
			'user.name' => 'required',
			'user.timezone' => 'required',
		];
	}

	public function mount() {
		$this->user = auth()->user();
	}

	public function submit() {
		toast()
            ->info('Settings updated')->push();
		$this->validate();
		$this->user->save();
		
        
	}

    public function render()
    {
        return view('livewire.settings.personal');
    }
}
