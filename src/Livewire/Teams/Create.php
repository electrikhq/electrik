<?php

namespace App\Livewire\Teams;

use App\Actions\Teams\CreateTeam;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    public $name = '';

    protected $rules = [
        'name' => 'required|string|max:255|unique:teams,name',
    ];

    public function create()
    {
        $this->validate();

        $action = new CreateTeam();
        $team = $action->execute(Auth::user(), $this->name);

        // Switch to the newly created team
        Auth::user()->update(['current_team_id' => $team->id]);

        session()->flash('message', 'Team created successfully.');

        return redirect()->route('teams.index');
    }

    public function render()
    {
        return view('livewire.teams.create')
            ->layout('layouts.app', [
                'title' => 'Create Team',
            ]);
    }
}

