<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Mpociot\Teamwork\Facades\Teamwork;
use Mpociot\Teamwork\TeamInvite;
use Usernotnull\Toast\Concerns\WireToast;

class Settings extends Component {

	use WireToast;

	public $team;
	
	protected function rules() {
		return [
			'team.name' => 'required|string|max:255',
		];
	}

	public function mount() {
		// dd(auth()->user()->currentTeam);
		$this->team = auth()->user()->currentTeam ?? new Team;
	}

	public function update() {
		
		$this->validate();
		$this->team->save();
		
		if ($this->team->hasStripeId()) {
			$this->team->syncStripeCustomerDetails();
        }
		
		toast()->success('Team updated successfully')->push();

	}
	
    public function render() {
        return view('livewire.teams.settings');
    }

}
