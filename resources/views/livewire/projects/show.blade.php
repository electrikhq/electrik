<div class="space-y-8">
    <x-electrik::page-header
        :title="$project->name"
        :description="$project->client
            ? __('Client: :name', ['name' => $project->client->name])
            : __('Internal project · :team', ['team' => $team->name])"
    >
        <x-slot:actions>
            <x-slate::button as="a" href="{{ route('projects.index') }}" variant="outline" size="sm" wire:navigate>
                {{ __('All projects') }}
            </x-slate::button>
        </x-slot:actions>
    </x-electrik::page-header>

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-border/80 bg-card p-4 shadow-xs">
            <p class="text-sm text-muted-foreground">{{ __('Status') }}</p>
            <p class="mt-1 font-semibold">{{ $project->statusLabel() }}</p>
        </div>
        <div class="rounded-xl border border-border/80 bg-card p-4 shadow-xs">
            <p class="text-sm text-muted-foreground">{{ __('Open tasks') }}</p>
            <p class="mt-1 text-2xl font-semibold tracking-tight">{{ number_format($openCount) }}</p>
        </div>
        <div class="rounded-xl border border-border/80 bg-card p-4 shadow-xs">
            <p class="text-sm text-muted-foreground">{{ __('Done') }}</p>
            <p class="mt-1 text-2xl font-semibold tracking-tight">{{ number_format($doneCount) }}</p>
        </div>
    </div>

    @if ($project->description)
        <p class="max-w-3xl text-sm text-muted-foreground">{{ $project->description }}</p>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
        <div class="space-y-3">
            <h2 class="text-sm font-medium text-muted-foreground">{{ __('Tasks') }}</h2>
            @forelse ($tasks as $task)
                <div class="rounded-xl border border-border/80 bg-card p-4 shadow-xs space-y-3" wire:key="task-{{ $task->id }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-medium {{ $task->status === 'done' ? 'text-muted-foreground line-through' : '' }}">{{ $task->title }}</p>
                            @if ($task->description)
                                <p class="mt-1 text-sm text-muted-foreground">{{ \Illuminate\Support\Str::limit($task->description, 160) }}</p>
                            @endif
                        </div>
                        <div class="flex shrink-0 flex-col items-end gap-1">
                            <x-slate::badge variant="{{ $task->status === 'done' ? 'secondary' : ($task->status === 'blocked' ? 'destructive' : 'outline') }}">
                                {{ $task->statusLabel() }}
                            </x-slate::badge>
                            @if ($task->priority === 'high')
                                <x-slate::badge variant="destructive">{{ __('High') }}</x-slate::badge>
                            @endif
                        </div>
                    </div>
                    <p class="text-xs text-muted-foreground">
                        @if ($task->assignee)
                            {{ $task->assignee->name }} ·
                        @endif
                        @if ($task->isOverdue())
                            <span class="text-destructive">{{ __('Overdue :date', ['date' => $task->due_on->toFormattedDateString()]) }}</span>
                        @elseif ($task->due_on)
                            {{ __('Due :date', ['date' => $task->due_on->toFormattedDateString()]) }}
                        @else
                            {{ __('No due date') }}
                        @endif
                    </p>
                    @if ($canManage)
                        <div class="flex flex-wrap gap-2">
                            @if ($task->status !== 'doing')
                                <x-slate::button type="button" size="sm" variant="outline" wire:click="setTaskStatus({{ $task->id }}, 'doing')">{{ __('Start') }}</x-slate::button>
                            @endif
                            @if ($task->status !== 'done')
                                <x-slate::button type="button" size="sm" variant="outline" wire:click="setTaskStatus({{ $task->id }}, 'done')">{{ __('Complete') }}</x-slate::button>
                            @endif
                            <x-slate::button type="button" size="sm" variant="ghost" wire:click="editTask({{ $task->id }})">{{ __('Edit') }}</x-slate::button>
                            <x-electrik::confirm
                                :title="__('Delete task?')"
                                :description="__('Remove :name from this project.', ['name' => $task->title])"
                                :confirm-label="__('Delete')"
                                wire-click="deleteTask({{ $task->id }})"
                            >
                                <x-slate::button type="button" size="sm" variant="destructive">{{ __('Delete') }}</x-slate::button>
                            </x-electrik::confirm>
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-border/80 p-8 text-center">
                    <p class="font-medium">{{ __('No tasks yet') }}</p>
                    <p class="mt-1 text-sm text-muted-foreground">{{ __('Break the project into to-dos on the right.') }}</p>
                </div>
            @endforelse
        </div>

        @if ($canManage)
            <x-slate::card class="h-fit border-border/80 shadow-xs">
                <x-slate::card-header>
                    <x-slate::card-title>{{ $editingTaskId ? __('Edit task') : __('Add task') }}</x-slate::card-title>
                    <x-slate::card-description>{{ __('Assign work to teammates on this project.') }}</x-slate::card-description>
                </x-slate::card-header>
                <x-slate::card-content>
                    <x-slate::form wire:submit="{{ $editingTaskId ? 'updateTask' : 'createTask' }}" class="space-y-4">
                        <x-slate::input wire:model="taskTitle" label="{{ __('Title') }}" required />
                        <x-slate::textarea wire:model="taskDescription" label="{{ __('Description') }}" rows="3" />
                        <div class="grid gap-4 sm:grid-cols-2">
                            <x-slate::select wire:model="taskStatus" label="{{ __('Status') }}">
                                <option value="todo">{{ __('To do') }}</option>
                                <option value="doing">{{ __('In progress') }}</option>
                                <option value="blocked">{{ __('Blocked') }}</option>
                                <option value="done">{{ __('Done') }}</option>
                            </x-slate::select>
                            <x-slate::select wire:model="taskPriority" label="{{ __('Priority') }}">
                                <option value="low">{{ __('Low') }}</option>
                                <option value="normal">{{ __('Normal') }}</option>
                                <option value="high">{{ __('High') }}</option>
                            </x-slate::select>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <x-slate::input wire:model="taskDueOn" type="date" label="{{ __('Due date') }}" />
                            <x-slate::select wire:model="taskAssigneeId" label="{{ __('Assignee') }}">
                                <option value="">{{ __('Unassigned') }}</option>
                                @foreach ($members as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </x-slate::select>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <x-slate::button type="submit">{{ $editingTaskId ? __('Save task') : __('Add task') }}</x-slate::button>
                            @if ($editingTaskId)
                                <x-slate::button type="button" variant="ghost" wire:click="cancelEdit">{{ __('Cancel') }}</x-slate::button>
                            @endif
                        </div>
                    </x-slate::form>
                </x-slate::card-content>
            </x-slate::card>
        @endif
    </div>
</div>
