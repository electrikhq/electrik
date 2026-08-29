<div class="space-y-8">
    <x-electrik::page-header
        title="{{ __('Projects') }}"
        :description="__('Delivery work for :team. Sample Studio micro-app you can replace with your product.', ['team' => $team->name])"
    >
        <x-slot:actions>
            @if ($canManage && ! $editingId)
                <x-slate::button type="button" size="sm" x-data x-on:click="$refs.create?.scrollIntoView({ behavior: 'smooth' })">
                    {{ __('New project') }}
                </x-slate::button>
            @endif
        </x-slot:actions>
    </x-electrik::page-header>

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3">
        <x-slate::select wire:model.live="status" class="h-9 w-auto min-w-40">
            <option value="">{{ __('All statuses') }}</option>
            <option value="active">{{ __('Active') }}</option>
            <option value="paused">{{ __('Paused') }}</option>
            <option value="done">{{ __('Done') }}</option>
        </x-slate::select>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        @forelse ($projects as $project)
            <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-3" wire:key="project-{{ $project->id }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <a href="{{ route('projects.show', $project) }}" class="font-semibold tracking-tight hover:underline" wire:navigate>
                            {{ $project->name }}
                        </a>
                        @if ($project->client)
                            <p class="mt-0.5 text-xs text-muted-foreground">{{ $project->client->name }}@if($project->client->company) · {{ $project->client->company }}@endif</p>
                        @endif
                        @if ($project->description)
                            <p class="mt-1 text-sm text-muted-foreground">{{ \Illuminate\Support\Str::limit($project->description, 120) }}</p>
                        @endif
                    </div>
                    <x-slate::badge variant="{{ $project->status === 'done' ? 'secondary' : ($project->status === 'paused' ? 'outline' : 'default') }}">
                        {{ $project->statusLabel() }}
                    </x-slate::badge>
                </div>
                <p class="text-xs text-muted-foreground">
                    {{ trans_choice('{0} No open tasks|{1} :count open task|[2,*] :count open tasks', $project->open_tasks_count, ['count' => $project->open_tasks_count]) }}
                    · {{ __('Due :date', ['date' => $project->due_on?->toFormattedDateString() ?? __('none')]) }}
                </p>
                <div class="flex flex-wrap gap-2 pt-1">
                    <x-slate::button as="a" href="{{ route('projects.show', $project) }}" size="sm" variant="outline" wire:navigate>
                        {{ __('Open') }}
                    </x-slate::button>
                    @if ($canManage)
                        <x-slate::button type="button" size="sm" variant="ghost" wire:click="edit({{ $project->id }})">
                            {{ __('Edit') }}
                        </x-slate::button>
                        <x-electrik::confirm
                            :title="__('Delete project?')"
                            :description="__('This also removes tasks on :name.', ['name' => $project->name])"
                            :confirm-label="__('Delete')"
                            wire-click="delete({{ $project->id }})"
                        >
                            <x-slate::button type="button" size="sm" variant="destructive">{{ __('Delete') }}</x-slate::button>
                        </x-electrik::confirm>
                    @endif
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-border/80 bg-card/50 p-8 text-center lg:col-span-2">
                <p class="font-medium">{{ __('No projects yet') }}</p>
                <p class="mt-1 text-sm text-muted-foreground">{{ __('Create a project, attach a client, then add tasks.') }}</p>
            </div>
        @endforelse
    </div>

    @if ($projects->hasPages())
        <div>{{ $projects->links() }}</div>
    @endif

    @if ($canManage)
        <x-slate::card class="max-w-xl border-border/80 shadow-xs" x-ref="create">
            <x-slate::card-header>
                <x-slate::card-title>{{ $editingId ? __('Edit project') : __('New project') }}</x-slate::card-title>
                <x-slate::card-description>{{ __('A scoped delivery container with tasks.') }}</x-slate::card-description>
            </x-slate::card-header>
            <x-slate::card-content>
                <x-slate::form wire:submit="{{ $editingId ? 'update' : 'create' }}" class="space-y-4">
                    <x-slate::input wire:model="name" label="{{ __('Name') }}" required />
                    <x-slate::textarea wire:model="description" label="{{ __('Description') }}" rows="3" />
                    <x-slate::select wire:model="clientId" label="{{ __('Client') }}">
                        <option value="">{{ __('No client') }}</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}@if($client->company) ({{ $client->company }})@endif</option>
                        @endforeach
                    </x-slate::select>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-slate::select wire:model="projectStatus" label="{{ __('Status') }}">
                            <option value="active">{{ __('Active') }}</option>
                            <option value="paused">{{ __('Paused') }}</option>
                            <option value="done">{{ __('Done') }}</option>
                        </x-slate::select>
                        <x-slate::input wire:model="dueOn" type="date" label="{{ __('Due date') }}" />
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <x-slate::button type="submit">{{ $editingId ? __('Save changes') : __('Create project') }}</x-slate::button>
                        @if ($editingId)
                            <x-slate::button type="button" variant="ghost" wire:click="cancelEdit">{{ __('Cancel') }}</x-slate::button>
                        @endif
                    </div>
                </x-slate::form>
            </x-slate::card-content>
        </x-slate::card>
    @endif
</div>
