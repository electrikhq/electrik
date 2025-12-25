<?php

namespace App\Livewire\Teams\Members;

use App\Models\Team;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public Team $team;

    public function mount(Team $team)
    {
        // Ensure user belongs to this team
        if (!Auth::user()->teams()->where('teams.id', $team->id)->exists()) {
            abort(403);
        }

        $this->team = $team;
    }

    public function removeMember($userId)
    {
        // Prevent removing the owner
        if ($this->team->owner_id == $userId) {
            session()->flash('error', 'Cannot remove the team owner.');
            return;
        }

        // Prevent removing yourself if you're the only member
        if ($userId == Auth::id() && $this->team->users()->count() == 1) {
            session()->flash('error', 'Cannot remove yourself as the only member.');
            return;
        }

        $this->team->users()->detach($userId);

        session()->flash('message', 'Member removed successfully.');
    }

    public function render()
    {
        // Load members with roles scoped to this team
        $members = $this->team->users()->with(['roles' => function ($query) {
            $query->where('roles.team_id', $this->team->id);
        }])->get();

        return view('livewire.teams.members.index', [
            'members' => $members,
        ])
            ->layout('layouts.app', [
                'title' => 'Team Members',
            ]);
    }
}

