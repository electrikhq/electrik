<?php

namespace App\Livewire\Billing;

use Livewire\Component;
use Usernotnull\Toast\Concerns\WireToast;

class Invoices extends Component {

	use WireToast;


	public $team;
	public $invoices;

	public function mount() {
		$this->team = auth()->user()->currentTeam;
		$this->invoices = $this->team->invoices();
		// dd($this->team->invoices());

	}

    public function render() {
        return view('livewire.billing.invoices');
    }

}
