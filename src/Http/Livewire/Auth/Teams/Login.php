<?php

namespace Electrik\Http\Livewire\Auth\Teams;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Electrik\Models\Role;
use Mpociot\Teamwork\Facades\Teamwork;

class Login extends Component
{
	public $invite;
	public $token;
	public $host;
	
	public $email, $password;

	function rules() {
		return [
			'email'=> 'email|required',
			'password'=> 'required',
		];
	}


	public function mount() {

		if(auth()->check()) return redirect()->route('dashboard.index');
		
		$this->token = session()->get('invite_token');
		$this->invite = Teamwork::getInviteFromAcceptToken($this->token);

	}

	public function login() {
		$credentials = $this->validate([
			'email' => ['required', 'email', function($attribute, $value, $fail) {
				if($value != $this->invite->email) {
					$fail("This email address does not match the invitation email address. Are you sure you have the right email?");
				}
			}],
            'password' => ['required'],
        ]);

		// dd($this->invite->team);
		// dd($this->invite);
		if (Auth::attempt($credentials)) {
            session()->regenerate();


			auth()->user()->attachTeam( $this->invite->team );
			auth()->user()->switchTeam( $this->invite->team->id );

			session(['team_id' => $this->invite->team->id]);
			setPermissionsTeamId(session('team_id'));
			
			$role = Role::findById($this->invite->role_id);
			// dd($role);
			auth()->user()->assignRole($role);

			$this->invite->delete();

            return redirect()->route('dashboard.index');	
			toast()->success('You have been added to the team successfully.')->pushOnNextPage();
			
		}
		$this->addError('email', 'The provided credentials do not match our records.');
	}

    public function render()
    {
        return view('electrik::livewire.auth.teams.login')->layout('electrik::layouts.livewire.guest');
    }
}
