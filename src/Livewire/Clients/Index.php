<?php

namespace Electrik\Livewire\Clients;

use Electrik\Concerns\AuthorizesTeamAccess;
use Electrik\Models\Client;
use Electrik\Support\ActivityLogger;
use Electrik\Support\SampleStudio;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('electrik::components.layouts.app')]
#[Title('Clients')]
class Index extends Component
{
    use AuthorizesTeamAccess;
    use WithPagination;

    #[Url]
    public string $status = '';

    public string $name = '';

    public string $email = '';

    public string $company = '';

    public string $clientStatus = 'active';

    public string $notes = '';

    public ?int $editingId = null;

    public function mount(): void
    {
        abort_unless(SampleStudio::enabled(), 404);

        $team = auth()->user()?->currentTeam;
        abort_unless($team, 404);
        $this->authorizeTeamPermission($team, 'teams.view');
    }

    public function create(): void
    {
        $team = auth()->user()->currentTeam;
        $this->authorizeTeamPermission($team, 'teams.manage');

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'clientStatus' => ['required', 'in:active,paused,archived'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $client = Client::query()->create([
            'team_id' => $team->id,
            'created_by' => auth()->id(),
            'name' => $validated['name'],
            'email' => $validated['email'] ?: null,
            'company' => $validated['company'] ?: null,
            'status' => $validated['clientStatus'],
            'notes' => $validated['notes'] ?: null,
        ]);

        ActivityLogger::log($team, 'client.created', auth()->user(), $client, [
            'name' => $client->name,
        ]);

        $this->resetForm();
        session()->flash('status', __('Client created.'));
    }

    public function edit(int $clientId): void
    {
        $team = auth()->user()->currentTeam;
        $this->authorizeTeamPermission($team, 'teams.manage');

        $client = Client::query()->whereKey($clientId)->firstOrFail();
        $this->editingId = $client->id;
        $this->name = $client->name;
        $this->email = (string) $client->email;
        $this->company = (string) $client->company;
        $this->clientStatus = $client->status;
        $this->notes = (string) $client->notes;
    }

    public function update(): void
    {
        $team = auth()->user()->currentTeam;
        $this->authorizeTeamPermission($team, 'teams.manage');

        $validated = $this->validate([
            'editingId' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'clientStatus' => ['required', 'in:active,paused,archived'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $client = Client::query()->whereKey($validated['editingId'])->firstOrFail();
        $client->update([
            'name' => $validated['name'],
            'email' => $validated['email'] ?: null,
            'company' => $validated['company'] ?: null,
            'status' => $validated['clientStatus'],
            'notes' => $validated['notes'] ?: null,
        ]);

        ActivityLogger::log($team, 'client.updated', auth()->user(), $client, [
            'name' => $client->name,
        ]);

        $this->resetForm();
        session()->flash('status', __('Client updated.'));
    }

    public function delete(int $clientId): void
    {
        $team = auth()->user()->currentTeam;
        $this->authorizeTeamPermission($team, 'teams.manage');

        $client = Client::query()->whereKey($clientId)->firstOrFail();
        ActivityLogger::log($team, 'client.deleted', auth()->user(), $client, [
            'name' => $client->name,
        ]);
        $client->delete();

        if ($this->editingId === $clientId) {
            $this->resetForm();
        }

        session()->flash('status', __('Client deleted.'));
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset('name', 'email', 'company', 'notes', 'editingId');
        $this->clientStatus = 'active';
    }

    public function render()
    {
        $team = auth()->user()->currentTeam;
        $this->bindTeamContext($team);

        $clients = Client::query()
            ->withCount('projects')
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(10);

        return view('electrik::livewire.clients.index', [
            'team' => $team,
            'clients' => $clients,
            'canManage' => auth()->user()->can('teams.manage') || auth()->user()->isOwnerOfTeam($team),
        ]);
    }
}
