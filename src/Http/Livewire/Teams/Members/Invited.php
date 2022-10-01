<?php

namespace Electrik\Http\Livewire\Teams\Members;

use Livewire\Component;

class Invited extends Component {

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

    public function render()
    {
        return view('electrik::livewire.teams.members.invited');
    }
}
