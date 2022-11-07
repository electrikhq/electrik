<?php

namespace App\Http\Livewire\Teams\Members;

use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Mpociot\Teamwork\Facades\Teamwork;
use Mpociot\Teamwork\TeamInvite;

class Index extends Component {

	public $team, $teamMembers;
	
	protected function rules() {
		return [
			'team.name' => 'required',
			'invite.email' => 'required',
		];
	}

	protected $listeners = ['UserInvitedToTeam' => 'userInvitedToTeam'];

	public function mount() {
		$this->team = auth()->user()->currentTeam;
	}

	public function userInvitedToTeam () {
		// dd('here');
	}

	public function openInvitationModal() {
		$this->emit('openModal', 'modals.teams.invite');
	}

	// public function update() {
		
	// 	$this->validate([
	// 		'team.name' => 'required',
	// 	]);

	// 	$this->team->save();

	// 	session()->flash('alert', [
	// 		'message' => 'Team updated successfully',
	// 		'color' => 'success',
	// 		'from' => 'update',
	// 	]);

	// 	if ($this->team->hasStripeId()) {
    //         $this->team->syncStripeCustomerDetails();
    //     }

	// }
	
	public function resendInvite($id) {
		$invite = TeamInvite::findOrFail($id);
        Mail::send('teamwork.emails.invite', ['team' => $invite->team, 'invite' => $invite], function ($m) use ($invite) {
            $m->to($invite->email)->subject('Invitation to join team '.$invite->team->name);
        });

        session()->flash('alert', [
			'message' => 'Team invitation resent successfully',
			'color' => 'success',
			'from' => 'resendInvite',
		]);
	}
	
	public function deleteInvite($id) {

		$invite = TeamInvite::findOrFail($id);
        
		$invite->delete();

        session()->flash('alert', [
			'message' => 'Team invitation deleted successfully',
			'color' => 'success',
			'from' => 'resendInvite',
		]);

		return redirect()->route('teams.settings');
	}

	public function invite() {
		$validatedData = $this->validate([
			'invite.email' => ['required', 'email', 'max:255',function($attribute, $value, $fail) {
				
				if(Teamwork::hasPendingInvite($value, $this->team)) {
					$fail('The email address is already invited to the team.');
				}

				if($value == auth()->user()->email) {
					$fail('You cannot invite yourself to the team');
				}

				if($this->team->invites()->count() + $this->team->users()->count() > 1) {
					$fail('You cannot add more users to this team');
					auth()->user()->notify(new UserNotification('You cannot add more users to this team'));
				}

			}]
		]);

		Teamwork::inviteToTeam($validatedData['invite']['email'], $this->team, function ($invite) {

			Mail::send('teamwork.emails.invite', ['team' => $invite->team, 'invite' => $invite], function ($m) use ($invite) {
				$m->to($invite->email)->subject('Invitation to join team '.$invite->team->name);
			});

			// Send email to user
			session()->flash('alert', [
				'message' => 'Team invitation sent successfully',
				'color' => 'success',
				'from' => 'invite',
			]);
		});

		// Teamwork::inviteToTeam( $validatedData['invite']['email'], auth()->user()->ownedTeams()->first(), function( $invite ) {
    	// 	// Send email to user / let them know that they got invited
		// 	// event(Send)
		// 	/* event listener is already listening to defaul event */
		// });

		// $this->team->save();
		
	}

    public function render()
    {
        return view('livewire.teams.members.index');
    }
}
