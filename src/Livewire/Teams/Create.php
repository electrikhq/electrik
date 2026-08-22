<?php

namespace Electrik\Livewire\Teams;

use Electrik\Support\EnsuresTeamRoles;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Create team')]
class Create extends Component
{
    public string $name = '';

    public function create(EnsuresTeamRoles $ensures): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $team = auth()->user()->createOwnedTeam(['name' => $validated['name']], true);

        $ensures->syncCatalog();
        $ensures->ensureForTeam($team);

        $this->redirect(route('teams.index'), navigate: true);
    }

    public function render()
    {
        return view('electrik::livewire.teams.create');
    }
}
