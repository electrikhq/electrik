<?php

namespace Electrik\Http\Livewire;

use Livewire\Component;

class HelloWorld extends Component {
	
	public $name;

	public function mount() {
		$this->name = 'World';
	}

	public function render() {
		return view('electrik::livewire.hello-world');
	}
}