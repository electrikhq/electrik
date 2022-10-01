<?php

namespace Electrik\Http\Livewire\Settings;

use Electrik\Models\Domain;
use Livewire\Component;

class Email extends Component {

	public $emailFromAddress, $emailFromName, $domains;

	function rules() {
		return [
			'emailFromAddress' => 'required|email',
			'emailFromName' => 'required|string|max:255',
		];
	}

	function mount() {
		$this->fromEmailAddress = auth()->user()->currentTeam->configuration()->where('key', 'from_email_address')->first()->value;
		$this->fromEmailName = auth()->user()->currentTeam->configuration()->where('key', 'from_email_name')->first()->value;
		$this->domains = Domain::whereRelation('team', 'id', '=', auth()->user()->currentTeam->id)->get();
	}

	public function submit() {
		toast()->info('All done')->push();
	}
    public function render() {
        return view('electrik::livewire.settings.email');
    }
}
