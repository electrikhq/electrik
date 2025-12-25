<?php

namespace App\Livewire\Teams\Invite;

use App\Actions\Teams\AcceptInvite;
use App\Models\TeamInvite;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Accept extends Component
{
    public TeamInvite $invite;

    public function mount($token)
    {
        $this->invite = TeamInvite::where('token', $token)->firstOrFail();

        // Check if invite is valid
        if (!$this->invite->isValid()) {
            session()->flash('error', 'This invitation has expired.');
            return;
        }

        // Check if user is authenticated
        if (!Auth::check()) {
            session()->flash('error', 'Please log in to accept this invitation.');
            return redirect()->route('login');
        }

        // Check if user email matches invite email
        if (Auth::user()->email !== $this->invite->email) {
            session()->flash('error', 'This invitation is not for your email address.');
            return;
        }

        // Check if user is already a member
        if ($this->invite->team->users()->where('users.id', Auth::id())->exists()) {
            session()->flash('error', 'You are already a member of this team.');
            return;
        }
    }

    public function accept()
    {
        $action = new AcceptInvite();
        $action->execute($this->invite, Auth::user());

        // Switch to the team
        Auth::user()->update(['current_team_id' => $this->invite->team->id]);

        session()->flash('message', 'Invitation accepted successfully.');

        return redirect()->route('dashboard.index');
    }

    public function render()
    {
        return view('livewire.teams.invite.accept')
            ->layout('layouts.app', [
                'title' => 'Accept Team Invitation',
            ]);
    }
}

