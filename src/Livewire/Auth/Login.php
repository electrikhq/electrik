<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component {

	public $email, $password;

	function rules() {
		return [
			'email'=> 'email|required',
			'password'=> 'required',
		];
	}

	public function mount() {
		if(auth()->check()) return redirect()->route('dashboard.index');
	}

    public function submit() {

		$credentials = $this->validate([
			'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

		if (Auth::attempt($credentials)) {
            session()->regenerate();

			// session(['team_id' => auth()->user()->currentTeam->id]);

            return redirect()->intended('dashboard');

		}
		$this->addError('email', 'The provided credentials do not match our records.');
	}

	public function render() {

        return view('livewire.auth.login')->layout('layouts.livewire.guest');
    }
}
