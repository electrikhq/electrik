<?php

namespace App\Livewire\Teams\Members;

use App\Actions\Teams\InviteMember;
use App\Models\Team;
use App\Models\Role;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Invite extends Component
{
    public Team $team;
    public $email = '';
    public $role_id = '';

    public function mount(Team $team)
    {
        // Ensure user belongs to this team
        if (!Auth::user()->teams()->where('teams.id', $team->id)->exists()) {
            abort(403);
        }

        $this->team = $team;
    }

    protected $rules = [
        'email' => 'required|email|max:255',
        'role_id' => 'nullable|exists:roles,id',
    ];

    public function invite()
    {
        $this->validate();

        // Check if user is already a member
        $existingUser = \App\Models\User::where('email', $this->email)->first();
        if ($existingUser && $this->team->users()->where('users.id', $existingUser->id)->exists()) {
            $this->addError('email', 'This user is already a member of the team.');
            return;
        }

        // Check team member limit
        if ($this->team->users()->count() >= $this->team->allowedMaxTeamMembers()) {
            $this->addError('email', 'Team member limit reached.');
            return;
        }

        $role = $this->role_id ? Role::find($this->role_id) : null;

        $action = new InviteMember();
        $invite = $action->execute($this->team, $this->email, $role);

        session()->flash('message', 'Invitation sent successfully.');

        $this->reset(['email', 'role_id']);
    }

    public function render()
    {
        $roles = Role::where('team_id', $this->team->id)->get();

        return view('livewire.teams.members.invite', [
            'roles' => $roles,
        ])
            ->layout('layouts.app', [
                'title' => 'Invite Team Member',
            ]);
    }
}

