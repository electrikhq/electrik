<?php

namespace Electrik\Livewire\Teams;

use Electrik\Actions\Teams\DeleteTeam;
use Electrik\Actions\Teams\TransferTeamOwnership;
use Electrik\Concerns\AuthorizesTeamAccess;
use Electrik\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Team settings')]
class Settings extends Component
{
    use AuthorizesTeamAccess;

    #[Locked]
    public Team $team;

    public string $name = '';

    public ?int $transferToUserId = null;

    public string $demoteRole = 'admin';

    public function mount(Team $team): void
    {
        $this->authorizeTeamPermission($team, 'teams.manage');
        $this->team = $team;
        $this->name = $team->name;
    }

    public function save(): void
    {
        $this->authorizeTeamPermission($this->team, 'teams.manage');

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $this->team->update(['name' => $validated['name']]);

        session()->flash('status', __('Team updated.'));
    }

    public function transferOwnership(): void
    {
        abort_unless(auth()->user()->isOwnerOfTeam($this->team), 403);

        $validated = $this->validate([
            'transferToUserId' => ['required', 'integer'],
            'demoteRole' => ['required', 'in:admin,member'],
        ]);

        $newOwner = $this->team->users()->whereKey($validated['transferToUserId'])->firstOrFail();

        app(TransferTeamOwnership::class)->execute(
            $this->team,
            auth()->user(),
            $newOwner,
            $validated['demoteRole']
        );

        $this->team->refresh();
        $this->reset('transferToUserId');

        session()->flash('status', __('Ownership transferred.'));
    }

    public function deleteTeam(): void
    {
        abort_unless(auth()->user()->isOwnerOfTeam($this->team), 403);

        $team = $this->team;

        app(DeleteTeam::class)->execute($team, auth()->user());

        session()->flash('status', __('Team deleted.'));

        $this->redirect(route('teams.index'), navigate: true);
    }

    public function render()
    {
        $this->bindTeamContext($this->team);

        $transferCandidates = $this->team->users()
            ->whereKeyNot($this->team->owner_id)
            ->orderBy('name')
            ->get();

        return view('electrik::livewire.teams.settings', [
            'isOwner' => auth()->user()->isOwnerOfTeam($this->team),
            'transferCandidates' => $transferCandidates,
        ]);
    }
}
