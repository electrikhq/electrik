<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class Store extends Component
{

	public $store;
	
	protected function rules() {
		return [
			'store.domain' => 'required',
			'store.name' => 'required',
			'store.currency' => 'required',
			'store.timezone' => 'required',
		];
	}

	public function mount() {
		$this->store = auth()->user()->currentTeam->stores()->first();
	}

	public function update() {
		$this->validate([
			'store.domain' => 'required',
			'store.name' => 'required',
			'store.currency' => 'required',
			'store.timezone' => 'required',
		]);
		$this->store->save();
		session()->flash('alert', [
			'message' => 'Store details updated successfully',
			'color' => 'success',
			'from' => 'update',
		]);
	}
	
	public function invite() {
		$validatedData = $this->validate([
			'invite.email' => 'required|email|max:255',
		]);

		// $this->team->save();
		session()->flash('alert', [
			'message' => 'Team invitation semt successfully',
			'color' => 'success',
			'from' => 'invite',
		]);
	}

    public function render()
    {
		if(!$this->store) 
			return view('empty-state');
			
        return view('livewire.settings.store');
    }
}
