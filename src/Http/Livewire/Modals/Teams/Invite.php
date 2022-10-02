<?php

namespace Electrik\Http\Livewire\Modals\Teams;

use Electrik\Models\Role;
use Electrik\Models\Team;
use Electrik\Models\TeamInvite;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Mail;
use LivewireUI\Modal\ModalComponent;
use Mpociot\Teamwork\Facades\Teamwork;
use Usernotnull\Toast\Concerns\WireToast;
use Mpociot\Teamwork\Events\UserInvitedToTeam;


class Invite extends ModalComponent {
	
	use WireToast;

    public $invite;
    public $roleId;

	// function rules() {
	// 	return [
	// 		'invite.email' => 'required|string|max:255',
	// 	];
	// }

    public function mount() {
		$this->team = auth()->user()->currentTeam;
    }

    public static function modalMaxWidth(): string {
        return '2xl';
    }


	public function invite() {
		$validatedData = $this->validate([
			'invite.email' => ['required', 'email', 'max:255', function($attribute, $value, $fail) {
				
				if(Teamwork::hasPendingInvite($value, $this->team)) {
					$fail('The email address is already invited to the team.');
				}

				if($value == auth()->user()->email) {
					$fail('You cannot invite yourself to the team');
				}

				if($this->team->invites()->count() + $this->team->users()->count() > $this->team->allowedMaxTeamMates()) {
					$fail('You cannot add more users to this team');
					// auth()->user()->notify(new UserNotification('You cannot add more users to this team'));
				}

			}],
			'roleId' => 'required',
		]);

		Teamwork::inviteToTeam($validatedData['invite']['email'], $this->team, function ($invite) {

			// dd($invite);
			$teamInvite = TeamInvite::find($invite->id);
			
			// dd($this->roleId);
			$teamInvite->role_id = $this->roleId;
			$teamInvite->save();

			// dd($teamInvite);
			event(new UserInvitedToTeam($invite));

			// Mail::send('teamwork.emails.invite', ['team' => $invite->team, 'invite' => $invite], function ($m) use ($invite) {
			// 	$m->to($invite->email)->subject('Invitation to join team '.$invite->team->name);
			// });

			// Send email to user
			toast()->success('Team invitation sent successfully')->push();

			$this->closeModal();
		});
	}

    public function render() {
        return view('electrik::livewire.modals.teams.invite', [
			'allRoles' => Role::all(),
        ]);
    }


}
