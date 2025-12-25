<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Settings extends Component
{
    public Team $team;
    public $name = '';

    public function mount(Team $team)
    {
        // Ensure user belongs to this team
        if (!Auth::user()->teams()->where('teams.id', $team->id)->exists()) {
            abort(403);
        }

        $this->team = $team;
        $this->name = $team->name;
    }

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:teams,name,' . $this->team->id,
        ]);

        $this->team->update([
            'name' => $this->name,
            'slug' => \Illuminate\Support\Str::slug($this->name),
        ]);

        session()->flash('message', 'Team settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.teams.settings')
            ->layout('layouts.app', [
                'title' => 'Team Settings',
            ]);
    }
}

