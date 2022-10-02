<?php

namespace Electrik\Http\Livewire\Auth\Teams;

use Electrik\Models\Team;
use Livewire\Component;

use Electrik\Models\Role;
use Electrik\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Mpociot\Teamwork\Facades\Teamwork;

class Register extends Component {

	
	public $invite;
	public $token;
	public $host;
	public $team;
	
	public $name, $email, $password;

	function rules() {
		return [
			'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', function($attribute, $value, $fail) {
				if($value != $this->invite->email) {
					$fail("This email address does not match the invitation email address. Are you sure you have the right email?");
				}
			}],
            'password' => ['required', Rules\Password::defaults()],
		];
	}


	public function mount() {

		if(auth()->check()) return redirect()->route('login');

		$this->token = session()->get('invite_token');
		$this->invite = Teamwork::getInviteFromAcceptToken($this->token);
		// dd($this->invite);

	}

	public function register() {

		$this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

		$user->attachTeam( $this->invite->team );
		$user->switchTeam( $this->invite->team->id );

        $this->invite->delete();

		Auth::login($user);


		session(['team_id' => auth()->user()->currentTeam->id]);
		setPermissionsTeamId(session('team_id'));


		$role = Role::findById($this->invite->role_id);
		$user->assignRole($role);


		return redirect()->route('dashboard.index');

	}

    public function render() {
        return view('electrik::livewire.auth.teams.register')->layout('electrik::layouts.livewire.guest');
    }
}
