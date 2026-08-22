<?php

namespace Electrik\Livewire\Teams;

use Electrik\Concerns\AuthorizesTeamAccess;
use Electrik\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Activity')]
class Activity extends Component
{
    use AuthorizesTeamAccess;

    #[Locked]
    public Team $team;

    public function mount(Team $team): void
    {
        $this->bindTeamContext($team);
        abort_unless(
            auth()->user()->can('teams.manage') || auth()->user()->isOwnerOfTeam($team),
            403
        );
        $this->team = $team;
    }

    public function render()
    {
        return view('electrik::livewire.teams.activity', [
            'entries' => $this->team->activityLogs()->with('user')->latest()->limit(50)->get(),
        ]);
    }
}
