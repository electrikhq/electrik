<?php

namespace Electrik\Livewire\Projects;

use Electrik\Concerns\AuthorizesTeamAccess;
use Electrik\Models\Client;
use Electrik\Models\Project;
use Electrik\Support\ActivityLogger;
use Electrik\Support\SampleStudio;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('electrik::components.layouts.app')]
#[Title('Projects')]
class Index extends Component
{
    use AuthorizesTeamAccess;
    use WithPagination;

    #[Url]
    public string $status = '';

    public string $name = '';

    public string $description = '';

    public string $projectStatus = 'active';

    public ?string $dueOn = null;

    public ?int $clientId = null;

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
            'description' => ['nullable', 'string', 'max:2000'],
            'projectStatus' => ['required', 'in:active,paused,done'],
            'dueOn' => ['nullable', 'date'],
            'clientId' => ['nullable', 'integer'],
        ]);

        $clientId = $this->resolveClientId($validated['clientId'] ?? null);

        $project = Project::query()->create([
            'team_id' => $team->id,
            'client_id' => $clientId,
            'created_by' => auth()->id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?: null,
            'status' => $validated['projectStatus'],
            'due_on' => $validated['dueOn'] ?: null,
        ]);

        ActivityLogger::log($team, 'project.created', auth()->user(), $project, [
            'name' => $project->name,
        ]);

        $this->resetForm();
        session()->flash('status', __('Project created.'));
        $this->redirect(route('projects.show', $project), navigate: true);
    }

    public function edit(int $projectId): void
    {
        $team = auth()->user()->currentTeam;
        $this->authorizeTeamPermission($team, 'teams.manage');

        $project = Project::query()->whereKey($projectId)->firstOrFail();
        $this->editingId = $project->id;
        $this->name = $project->name;
        $this->description = (string) $project->description;
        $this->projectStatus = $project->status;
        $this->dueOn = $project->due_on?->format('Y-m-d');
        $this->clientId = $project->client_id;
    }

    public function update(): void
    {
        $team = auth()->user()->currentTeam;
        $this->authorizeTeamPermission($team, 'teams.manage');

        $validated = $this->validate([
            'editingId' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'projectStatus' => ['required', 'in:active,paused,done'],
            'dueOn' => ['nullable', 'date'],
            'clientId' => ['nullable', 'integer'],
        ]);

        $project = Project::query()->whereKey($validated['editingId'])->firstOrFail();
        $project->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?: null,
            'status' => $validated['projectStatus'],
            'due_on' => $validated['dueOn'] ?: null,
            'client_id' => $this->resolveClientId($validated['clientId'] ?? null),
        ]);

        ActivityLogger::log($team, 'project.updated', auth()->user(), $project, [
            'name' => $project->name,
        ]);

        $this->resetForm();
        session()->flash('status', __('Project updated.'));
    }

    public function delete(int $projectId): void
    {
        $team = auth()->user()->currentTeam;
        $this->authorizeTeamPermission($team, 'teams.manage');

        $project = Project::query()->whereKey($projectId)->firstOrFail();
        ActivityLogger::log($team, 'project.deleted', auth()->user(), $project, [
            'name' => $project->name,
        ]);
        $project->delete();

        if ($this->editingId === $projectId) {
            $this->resetForm();
        }

        session()->flash('status', __('Project deleted.'));
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    protected function resolveClientId(?int $clientId): ?int
    {
        if (! $clientId) {
            return null;
        }

        return Client::query()->whereKey($clientId)->exists() ? $clientId : null;
    }

    protected function resetForm(): void
    {
        $this->reset('name', 'description', 'dueOn', 'clientId', 'editingId');
        $this->projectStatus = 'active';
    }

    public function render()
    {
        $team = auth()->user()->currentTeam;
        $this->bindTeamContext($team);

        $projects = Project::query()
            ->with(['creator', 'client'])
            ->withCount([
                'tasks',
                'tasks as open_tasks_count' => fn ($q) => $q->where('status', '!=', 'done'),
            ])
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(10);

        return view('electrik::livewire.projects.index', [
            'team' => $team,
            'projects' => $projects,
            'clients' => Client::query()->orderBy('name')->get(['id', 'name', 'company']),
            'canManage' => auth()->user()->can('teams.manage') || auth()->user()->isOwnerOfTeam($team),
        ]);
    }
}
