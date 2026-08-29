<?php

namespace Electrik\Livewire\Teams;

use Electrik\Actions\Teams\ArchiveTeam;
use Electrik\Actions\Teams\DeleteTeam;
use Electrik\Actions\Teams\RestoreTeam;
use Electrik\Actions\Teams\TransferTeamOwnership;
use Electrik\Concerns\AuthorizesTeamAccess;
use Electrik\Models\Team;
use Electrik\Support\ActivityLogger;
use Electrik\Support\TeamWebhookDispatcher;
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

    public $brandLogo;

    public ?string $brandPrimary = null;

    public string $allowedIpsText = '';

    public ?int $transferToUserId = null;

    public string $demoteRole = 'admin';

    public function mount(Team $team): void
    {
        $this->authorizeTeamPermission($team, 'teams.manage');
        $this->team = $team;
        $this->name = $team->name;
        $this->brandPrimary = $team->brand_primary;
        $this->allowedIpsText = implode("\n", $team->allowedIpList());
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

    public function saveBranding(): void
    {
        $this->authorizeTeamPermission($this->team, 'teams.manage');

        $this->brandPrimary = trim((string) $this->brandPrimary) ?: null;

        $validated = $this->validate([
            'brandPrimary' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $this->team->update(['brand_primary' => $validated['brandPrimary']]);
        $this->team->refresh();
        $this->brandPrimary = $this->team->brand_primary;

        ActivityLogger::log($this->team, 'team.branding_updated', auth()->user(), $this->team);

        $this->dispatch('electrik:brand-updated', primary: $this->brandPrimary);
        $this->js(
            'window.dispatchEvent(new CustomEvent("electrik:brand-updated", { detail: '.json_encode(['primary' => $this->brandPrimary]).' }))'
        );

        session()->flash('status', __('Branding updated.'));
    }

    public function updateBrandLogo(): void
    {
        $this->authorizeTeamPermission($this->team, 'teams.manage');

        $this->validate([
            'brandLogo' => ['required', 'image', 'max:2048'],
        ]);

        $path = $this->brandLogo->store('team-branding/'.$this->team->id, 'public');

        if ($this->team->brand_logo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($this->team->brand_logo_path);
        }

        $this->team->update(['brand_logo_path' => $path]);
        $this->reset('brandLogo');

        ActivityLogger::log($this->team, 'team.brand_logo_updated', auth()->user(), $this->team);

        session()->flash('status', __('Brand logo updated.'));
    }

    public function removeBrandLogo(): void
    {
        $this->authorizeTeamPermission($this->team, 'teams.manage');

        if ($this->team->brand_logo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($this->team->brand_logo_path);
            $this->team->update(['brand_logo_path' => null]);
        }

        ActivityLogger::log($this->team, 'team.brand_logo_removed', auth()->user(), $this->team);

        session()->flash('status', __('Brand logo removed.'));
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

    public function saveAllowedIps(): void
    {
        $this->authorizeTeamPermission($this->team, 'teams.manage');

        $ips = collect(preg_split('/[\s,]+/', $this->allowedIpsText) ?: [])
            ->map(fn ($ip) => trim((string) $ip))
            ->filter()
            ->unique()
            ->values()
            ->all();

        foreach ($ips as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
                $this->addError('allowedIpsText', __('":ip" is not a valid IP address.', ['ip' => $ip]));

                return;
            }
        }

        $this->team->update(['allowed_ips' => $ips === [] ? null : $ips]);
        $this->allowedIpsText = implode("\n", $ips);

        ActivityLogger::log($this->team, 'team.allowed_ips_updated', auth()->user(), $this->team, [
            'count' => count($ips),
        ]);

        session()->flash('status', __('Login IP allowlist updated.'));
    }

    public function archiveTeam(): void
    {
        abort_unless(auth()->user()->isOwnerOfTeam($this->team), 403);

        app(ArchiveTeam::class)->execute($this->team, auth()->user());

        $this->team->refresh();

        session()->flash('status', __('Team archived.'));
    }

    public function restoreTeam(): void
    {
        abort_unless(auth()->user()->isOwnerOfTeam($this->team), 403);

        app(RestoreTeam::class)->execute($this->team, auth()->user());

        $this->team->refresh();

        session()->flash('status', __('Team restored.'));
    }

    public function deleteTeam(): void
    {
        abort_unless(auth()->user()->isOwnerOfTeam($this->team), 403);

        $team = $this->team;

        ActivityLogger::log($team, 'team.deleted', auth()->user(), $team);
        TeamWebhookDispatcher::dispatch($team, 'team.deleted', [
            'team_id' => $team->id,
            'name' => $team->name,
        ]);

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
