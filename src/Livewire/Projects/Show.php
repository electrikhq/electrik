<?php

namespace Electrik\Livewire\Projects;

use Electrik\Concerns\AuthorizesTeamAccess;
use Electrik\Models\Project;
use Electrik\Models\Task;
use Electrik\Support\ActivityLogger;
use Electrik\Support\SampleStudio;
use Electrik\Support\UserModel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Project')]
class Show extends Component
{
    use AuthorizesTeamAccess;

    public Project $project;

    public string $taskTitle = '';

    public string $taskDescription = '';

    public string $taskStatus = 'todo';

    public string $taskPriority = 'normal';

    public ?string $taskDueOn = null;

    public ?int $taskAssigneeId = null;

    public ?int $editingTaskId = null;

    public function mount(Project $project): void
    {
        abort_unless(SampleStudio::enabled(), 404);

        $team = auth()->user()?->currentTeam;
        abort_unless($team, 404);
        $this->authorizeTeamPermission($team, 'teams.view');

        abort_unless((int) $project->team_id === (int) $team->id, 404);

        $this->project = $project->load(['client', 'creator']);
    }

    public function createTask(): void
    {
        $team = auth()->user()->currentTeam;
        $this->authorizeTeamPermission($team, 'teams.manage');

        $validated = $this->validate([
            'taskTitle' => ['required', 'string', 'max:255'],
            'taskDescription' => ['nullable', 'string', 'max:2000'],
            'taskStatus' => ['required', 'in:todo,doing,done,blocked'],
            'taskPriority' => ['required', 'in:low,normal,high'],
            'taskDueOn' => ['nullable', 'date'],
            'taskAssigneeId' => ['nullable', 'integer'],
        ]);

        $assigneeId = $validated['taskAssigneeId'] ?: null;
        if ($assigneeId) {
            $userModel = UserModel::class();
            $assignee = $userModel::query()->find($assigneeId);
            if (! $assignee || ! $team->hasUser($assignee)) {
                $this->addError('taskAssigneeId', __('Assignee must be a team member.'));

                return;
            }
        }

        $task = Task::query()->create([
            'team_id' => $team->id,
            'project_id' => $this->project->id,
            'created_by' => auth()->id(),
            'assignee_id' => $assigneeId,
            'title' => $validated['taskTitle'],
            'description' => $validated['taskDescription'] ?: null,
            'status' => $validated['taskStatus'],
            'priority' => $validated['taskPriority'],
            'due_on' => $validated['taskDueOn'] ?: null,
            'completed_at' => $validated['taskStatus'] === 'done' ? now() : null,
        ]);

        ActivityLogger::log($team, 'task.created', auth()->user(), $task, [
            'name' => $task->title,
            'project' => $this->project->name,
        ]);

        $this->resetTaskForm();
        session()->flash('status', __('Task created.'));
    }

    public function editTask(int $taskId): void
    {
        $team = auth()->user()->currentTeam;
        $this->authorizeTeamPermission($team, 'teams.manage');

        $task = Task::query()->where('project_id', $this->project->id)->whereKey($taskId)->firstOrFail();
        $this->editingTaskId = $task->id;
        $this->taskTitle = $task->title;
        $this->taskDescription = (string) $task->description;
        $this->taskStatus = $task->status;
        $this->taskPriority = $task->priority;
        $this->taskDueOn = $task->due_on?->format('Y-m-d');
        $this->taskAssigneeId = $task->assignee_id;
    }

    public function updateTask(): void
    {
        $team = auth()->user()->currentTeam;
        $this->authorizeTeamPermission($team, 'teams.manage');

        $validated = $this->validate([
            'editingTaskId' => ['required', 'integer'],
            'taskTitle' => ['required', 'string', 'max:255'],
            'taskDescription' => ['nullable', 'string', 'max:2000'],
            'taskStatus' => ['required', 'in:todo,doing,done,blocked'],
            'taskPriority' => ['required', 'in:low,normal,high'],
            'taskDueOn' => ['nullable', 'date'],
            'taskAssigneeId' => ['nullable', 'integer'],
        ]);

        $task = Task::query()
            ->where('project_id', $this->project->id)
            ->whereKey($validated['editingTaskId'])
            ->firstOrFail();

        $assigneeId = $validated['taskAssigneeId'] ?: null;
        if ($assigneeId) {
            $userModel = UserModel::class();
            $assignee = $userModel::query()->find($assigneeId);
            if (! $assignee || ! $team->hasUser($assignee)) {
                $this->addError('taskAssigneeId', __('Assignee must be a team member.'));

                return;
            }
        }

        $task->update([
            'title' => $validated['taskTitle'],
            'description' => $validated['taskDescription'] ?: null,
            'status' => $validated['taskStatus'],
            'priority' => $validated['taskPriority'],
            'due_on' => $validated['taskDueOn'] ?: null,
            'assignee_id' => $assigneeId,
            'completed_at' => $validated['taskStatus'] === 'done'
                ? ($task->completed_at ?? now())
                : null,
        ]);

        ActivityLogger::log($team, 'task.updated', auth()->user(), $task, [
            'name' => $task->title,
            'project' => $this->project->name,
        ]);

        $this->resetTaskForm();
        session()->flash('status', __('Task updated.'));
    }

    public function setTaskStatus(int $taskId, string $status): void
    {
        $team = auth()->user()->currentTeam;
        $this->authorizeTeamPermission($team, 'teams.manage');

        if (! in_array($status, ['todo', 'doing', 'done', 'blocked'], true)) {
            return;
        }

        $task = Task::query()->where('project_id', $this->project->id)->whereKey($taskId)->firstOrFail();
        $task->update([
            'status' => $status,
            'completed_at' => $status === 'done' ? ($task->completed_at ?? now()) : null,
        ]);

        ActivityLogger::log($team, 'task.updated', auth()->user(), $task, [
            'name' => $task->title,
            'project' => $this->project->name,
        ]);
    }

    public function deleteTask(int $taskId): void
    {
        $team = auth()->user()->currentTeam;
        $this->authorizeTeamPermission($team, 'teams.manage');

        $task = Task::query()->where('project_id', $this->project->id)->whereKey($taskId)->firstOrFail();
        ActivityLogger::log($team, 'task.deleted', auth()->user(), $task, [
            'name' => $task->title,
            'project' => $this->project->name,
        ]);
        $task->delete();

        if ($this->editingTaskId === $taskId) {
            $this->resetTaskForm();
        }

        session()->flash('status', __('Task deleted.'));
    }

    public function cancelEdit(): void
    {
        $this->resetTaskForm();
    }

    protected function resetTaskForm(): void
    {
        $this->reset('taskTitle', 'taskDescription', 'taskDueOn', 'taskAssigneeId', 'editingTaskId');
        $this->taskStatus = 'todo';
        $this->taskPriority = 'normal';
    }

    public function render()
    {
        $team = auth()->user()->currentTeam;
        $this->bindTeamContext($team);

        $tasks = Task::query()
            ->with('assignee')
            ->where('project_id', $this->project->id)
            ->orderByRaw("case status when 'blocked' then 0 when 'doing' then 1 when 'todo' then 2 else 3 end")
            ->orderBy('due_on')
            ->get();

        $members = $team->users()->orderBy('name')->get();

        return view('electrik::livewire.projects.show', [
            'team' => $team,
            'tasks' => $tasks,
            'members' => $members,
            'canManage' => auth()->user()->can('teams.manage') || auth()->user()->isOwnerOfTeam($team),
            'openCount' => $tasks->where('status', '!=', 'done')->count(),
            'doneCount' => $tasks->where('status', 'done')->count(),
        ]);
    }
}
