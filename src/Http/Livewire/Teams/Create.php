<?php

namespace Electrik\Http\Livewire\Teams;

use Illuminate\Support\Facades\Artisan;
use Livewire\Component;

class Create extends Component {

	public $name;

	function rules() {
		return [
			'name' => [
				'required', 
				'string', 
				'max:255', 
				// 'unique:teams,name,NULL,owner_id,'.auth()->user()->id, 
				function($attribute, $value, $fail) {

					if(auth()->user()->ownedTeams()->count() >= 1) {
						$fail('You cannot create more than one team.');
					}

				}
			]
		];
	}

	public function create() {

		$this->validate();

		$teamModel = config('teamwork.team_model');

        $team = $teamModel::create([
            'name' => $this->name,
            'owner_id' => auth()->user()->getKey(),
        ]);

        auth()->user()->attachTeam($team);
		
        auth()->user()->switchTeam($team);

		Artisan::queue('setup:teams:default', ['team' => $team->id]);

		toast()->success('Team created successfully')->pushOnNextPage();

        return redirect(route('teams.settings'));
	}

    public function render() {
        return view('electrik::livewire.teams.create');
    }
}
