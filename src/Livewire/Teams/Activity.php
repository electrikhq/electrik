<?php

namespace Electrik\Livewire\Teams;

use Electrik\Concerns\AuthorizesTeamAccess;
use Electrik\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('electrik::components.layouts.app')]
#[Title('Activity')]
class Activity extends Component
{
    use AuthorizesTeamAccess;
    use WithPagination;

    #[Locked]
    public Team $team;

    #[Url]
    public string $actionFilter = '';

    public function mount(Team $team): void
    {
        $this->bindTeamContext($team);
        abort_unless(
            auth()->user()->can('teams.manage') || auth()->user()->isOwnerOfTeam($team),
            403
        );
        $this->team = $team;
    }

    public function updatingActionFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = $this->team->activityLogs()->with('causer')->latest();

        if ($this->actionFilter !== '') {
            $query->where('event', $this->actionFilter);
        }

        $actions = $this->team->activityLogs()
            ->select('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');

        return view('electrik::livewire.teams.activity', [
            'entries' => $query->paginate(20),
            'actions' => $actions,
        ]);
    }
}
