<?php

namespace Electrik\Livewire\Teams;

use Electrik\Actions\Teams\DeleteTeam;
use Electrik\Actions\Teams\TransferTeamOwnership;
use Electrik\Concerns\AuthorizesTeamAccess;
use Electrik\Models\Team;
use Electrik\Support\ActivityLogger;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('electrik::components.layouts.app')]
#[Title('Team settings')]
class Settings extends Component
{
    use AuthorizesTeamAccess;
    use WithFileUploads;

    #[Locked]
    public Team $team;

    public string $name = '';

    public $avatar;

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

        ActivityLogger::log($this->team, 'team.updated', auth()->user(), $this->team);

        session()->flash('status', __('Team updated.'));
    }

    public function updateAvatar(): void
    {
        $this->authorizeTeamPermission($this->team, 'teams.manage');

        $this->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $path = $this->avatar->store('team-avatars/'.$this->team->id, 'public');

        $this->team->update(['avatar_path' => $path]);
        $this->reset('avatar');

        ActivityLogger::log($this->team, 'team.avatar_updated', auth()->user(), $this->team);

        session()->flash('status', __('Team avatar updated.'));
    }

    public function removeAvatar(): void
    {
        $this->authorizeTeamPermission($this->team, 'teams.manage');

        if ($this->team->avatar_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($this->team->avatar_path);
            $this->team->update(['avatar_path' => null]);
        }

        session()->flash('status', __('Team avatar removed.'));
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

        ActivityLogger::log(
            $this->team,
            'team.ownership_transferred',
            auth()->user(),
            $newOwner,
            ['demote_role' => $validated['demoteRole']]
        );

        $this->team->refresh();
        $this->reset('transferToUserId');

        session()->flash('status', __('Ownership transferred.'));
    }

    public function deleteTeam(): void
    {
        abort_unless(auth()->user()->isOwnerOfTeam($this->team), 403);

        $team = $this->team;

        ActivityLogger::log($team, 'team.deleted', auth()->user(), $team);

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
