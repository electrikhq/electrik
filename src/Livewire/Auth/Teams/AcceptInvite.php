<?php

namespace App\Livewire\Auth\Teams;

use Livewire\Component;
use Mpociot\Teamwork\Facades\Teamwork;

class AcceptInvite extends Component {

	public function mount($token) {
        
		// dd($token);
		$invite = Teamwork::getInviteFromAcceptToken($token);

		// dd($invite);
        if (! $invite) {
            abort(404);
        }

		// dd($invite);
		// dd(auth()->user()->whereHas('teams', function($query) use($invite) {
		// 	$query->where('teams.id', $invite->team_id);
		// })->exists());
        if (auth()->check()) {
			if(auth()->user()->email != $invite->email) {
				// dd('alsd');

				session()->flash('systemAlerts', [
					'message' => 'You cannot join the team as someone else',
					'color' => 'danger',
				]);

			}elseif(
				auth()->user()->whereHas('teams', function($query) use($invite) {
					$query->where('teams.id', $invite->team_id);
				})->exists()
			) {
				session()->flash('systemAlerts', [
					'message' => 'You are already a member of this team',
					'color' => 'danger',
				]);
			} else {
				Teamwork::acceptInvite($invite);
				auth()->user()->switchTeam($invite->team_id);
				// dd($invite);
				$invite->delete();
				toast()->success('You have been added to the team successfully.')->pushOnNextPage();
			}
			

            return redirect()->route('dashboard.index');

        } else {

            session(['invite_token' => $token]);
            return redirect()->route('teams.invite.login');

        }
    }

    public function render() {
        return view('livewire.auth.teams.accept-invite');
    }
}
