<?php

namespace Electrik\Http\Livewire\Onboarding;

use Electrik\Models\Team;
use Electrik\Models\Permission;
use Electrik\Models\Role;
use Livewire\Component;

use Electrik\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Usernotnull\Toast\Concerns\WireToast;


class Register extends Component {

	use WireToast;
	public $name, $email, $password, $selectedPlan, $plan;

	protected function rules() {
		return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
        ];
	}

	protected $queryString = [
        'plan',
    ];


	public function mount() {

		$this->selectedPlan = collect(config('plans.billables.team.plans'))->where('slug', (request()->plan) ? request()->plan : 'sprrw.core')->first();

		if(!$this->selectedPlan) {
			$this->selectedPlan = collect(config('plans.billables.team.plans'))->where('slug', 'sprrw.core')->first();
		}

	}

	public function submit() {

		// dd($this->getOriginalPlan());
		$this->validate();

		// dd($this->getOriginalPlan());
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
			'original_plan' => $this->getOriginalPlan(),
        ]);

		$team = new Team;
		$team->owner_id = $user->id;
		$team->name = $user->name . "'s team";
		$team->save();

		$user->teams()->attach($team->id); // id only

		$user->switchTeam( $team->id );

		$stripeCustomer = $team->createOrGetStripeCustomer();

        event(new Registered($user));

        Auth::login($user);

		// dd(auth()->user());

		/* create default role */

		session(['team_id' => auth()->user()->currentTeam->id]);
		setPermissionsTeamId(session('team_id'));


		$role = Role::create([
			'name' => 'administrators',
			'team_id' => $team->id,
		]);
		
		$allPermissions = Permission::all();

		$role->permissions()->sync($allPermissions);
		$user->assignRole($role);

		$user->currentTeam->configuration()->create([
			'key' => 'settings.system.admin_role_id',
			'value' => $role->id,
		]);

		// dd('redirecting to confirm');

		return redirect()->route('onboarding.confirm');

		// session()->flash('message', [
		// 	'message' => 'Account Regiereted Successfuly',
		// ])

        // return response()->noContent();
		
	}

	function getOriginalPlan() {
		return $this->plan;
	}

    public function render()
    {
        return view('electrik::livewire.onboarding.register')->layout('electrik::layouts.livewire.onboarding');
    }
}
