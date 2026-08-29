<div class="space-y-8">
    <x-electrik::page-header
        title="{{ __('Studio') }}"
        :description="$user?->name
            ? __('Welcome, :name. Client work, projects, and tasks for this team.', ['name' => $user->name])
            : __('Client work, projects, and tasks for this team.')"
    >
        @if ($studioEnabled)
            <x-slot:actions>
                <x-slate::button as="a" href="{{ route('clients.index') }}" variant="outline" size="sm" wire:navigate>
                    {{ __('Clients') }}
                </x-slate::button>
                <x-slate::button as="a" href="{{ route('projects.index') }}" size="sm" wire:navigate>
                    {{ __('Projects') }}
                </x-slate::button>
            </x-slot:actions>
        @endif
    </x-electrik::page-header>

    @if (request()->boolean('verified'))
        <x-slate::alert variant="success" title="{{ __('Email verified') }}" description="{{ __('Your email address is confirmed.') }}" />
    @endif

    @if ($needsSubscription)
        <x-slate::alert variant="destructive" :title="__('Subscription required')">
            <p class="text-sm">
                {{ __('Choose a plan to unlock the full app.') }}
                <a href="{{ route('billing.plans') }}" class="font-medium underline underline-offset-4" wire:navigate>{{ __('View plans') }}</a>
            </p>
        </x-slate::alert>
    @endif

    @if ($studioEnabled)
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-1">
                <p class="text-sm font-medium text-muted-foreground">{{ __('Active clients') }}</p>
                <p class="text-2xl font-semibold tracking-tight">{{ number_format($studio['clients']) }}</p>
                <a href="{{ route('clients.index') }}" class="text-xs text-muted-foreground underline-offset-4 hover:underline" wire:navigate>{{ __('Manage clients') }}</a>
            </div>
            <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-1">
                <p class="text-sm font-medium text-muted-foreground">{{ __('Active projects') }}</p>
                <p class="text-2xl font-semibold tracking-tight">{{ number_format($studio['projects_active']) }}</p>
                <a href="{{ route('projects.index') }}" class="text-xs text-muted-foreground underline-offset-4 hover:underline" wire:navigate>{{ __('View projects') }}</a>
            </div>
            <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-1">
                <p class="text-sm font-medium text-muted-foreground">{{ __('Open tasks') }}</p>
                <p class="text-2xl font-semibold tracking-tight">{{ number_format($studio['tasks_open']) }}</p>
            </div>
            <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-1">
                <p class="text-sm font-medium text-muted-foreground">{{ __('Overdue') }}</p>
                <p class="text-2xl font-semibold tracking-tight {{ $studio['tasks_overdue'] > 0 ? 'text-destructive' : '' }}">{{ number_format($studio['tasks_overdue']) }}</p>
            </div>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-1">
                <p class="text-sm font-medium text-muted-foreground">{{ __('Members') }}</p>
                <p class="text-2xl font-semibold tracking-tight">{{ number_format($memberCount) }}</p>
            </div>
            <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-1">
                <p class="text-sm font-medium text-muted-foreground">{{ __('Pending invites') }}</p>
                <p class="text-2xl font-semibold tracking-tight">{{ number_format($pendingInvites) }}</p>
            </div>
            <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-1">
                <p class="text-sm font-medium text-muted-foreground">{{ __('Plan') }}</p>
                <p class="text-2xl font-semibold tracking-tight truncate">{{ $plan?->name ?? $statusLabel }}</p>
            </div>
        </div>
    @endif

    @if ($studioEnabled)
        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-4">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="text-sm font-medium text-muted-foreground">{{ __('My open tasks') }}</h2>
                </div>
                <div class="space-y-3">
                    @forelse ($myTasks as $task)
                        <a href="{{ route('projects.show', $task->project) }}" class="block border-b border-border/60 pb-3 last:border-0 last:pb-0 hover:opacity-90" wire:navigate wire:key="my-task-{{ $task->id }}">
                            <p class="font-medium">{{ $task->title }}</p>
                            <p class="text-xs text-muted-foreground">
                                {{ $task->project?->name }}
                                @if ($task->isOverdue())
                                    · <span class="text-destructive">{{ __('Overdue') }}</span>
                                @elseif ($task->due_on)
                                    · {{ __('Due :date', ['date' => $task->due_on->toFormattedDateString()]) }}
                                @endif
                            </p>
                        </a>
                    @empty
                        <p class="text-sm text-muted-foreground">{{ __('Nothing assigned to you right now.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-4">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="text-sm font-medium text-muted-foreground">{{ __('Overdue across the team') }}</h2>
                </div>
                <div class="space-y-3">
                    @forelse ($overdueTasks as $task)
                        <a href="{{ route('projects.show', $task->project) }}" class="block border-b border-border/60 pb-3 last:border-0 last:pb-0 hover:opacity-90" wire:navigate wire:key="overdue-{{ $task->id }}">
                            <p class="font-medium">{{ $task->title }}</p>
                            <p class="text-xs text-muted-foreground">
                                {{ $task->project?->name }}
                                · {{ $task->assignee?->name ?? __('Unassigned') }}
                                · {{ $task->due_on?->toFormattedDateString() }}
                            </p>
                        </a>
                    @empty
                        <p class="text-sm text-muted-foreground">{{ __('No overdue tasks. Nice.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-3">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-sm font-medium text-muted-foreground">{{ __('Workspace') }}</h2>
                @if ($team)
                    <x-slate::badge variant="secondary">{{ __('Current') }}</x-slate::badge>
                @endif
            </div>
            @if ($team)
                <p class="text-lg font-semibold tracking-tight">{{ $team->name }}</p>
                <p class="text-sm text-muted-foreground">
                    {{ trans_choice('{1} :count member|[2,*] :count members', $memberCount, ['count' => $memberCount]) }}
                    @if ($pendingInvites)
                        · {{ trans_choice('{1} :count invite|[2,*] :count invites', $pendingInvites, ['count' => $pendingInvites]) }}
                    @endif
                </p>
                <div class="flex flex-wrap gap-2 pt-1">
                    <x-slate::button as="a" href="{{ route('teams.members', $team) }}" variant="outline" size="sm" wire:navigate>
                        {{ __('Members') }}
                    </x-slate::button>
                    <x-slate::button as="a" href="{{ route('teams.settings', $team) }}" variant="outline" size="sm" wire:navigate>
                        {{ __('Settings') }}
                    </x-slate::button>
                </div>
            @else
                <p class="text-sm text-muted-foreground">{{ __('No team selected.') }}</p>
                <x-slate::button as="a" href="{{ route('teams.create') }}" size="sm" wire:navigate>{{ __('Create team') }}</x-slate::button>
            @endif
        </div>

        <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-3">
            <h2 class="text-sm font-medium text-muted-foreground">{{ __('Billing') }}</h2>
            @if ($plan)
                <p class="text-lg font-semibold tracking-tight">{{ $plan->name }}</p>
                <p class="text-sm text-muted-foreground">{{ $plan->formatted_price }}/{{ $plan->interval }}</p>
            @else
                <p class="text-lg font-semibold tracking-tight">{{ $statusLabel }}</p>
            @endif
            @if ($trialLabel)
                <x-slate::badge variant="secondary">{{ $trialLabel }}</x-slate::badge>
            @elseif ($subscription?->active())
                <p class="text-sm text-muted-foreground">{{ __('Subscription is active for this team.') }}</p>
            @else
                <p class="text-sm text-muted-foreground">{{ __('Choose a plan when you are ready to upgrade.') }}</p>
            @endif
            <div class="pt-1">
                <x-slate::button as="a" href="{{ route('billing.index') }}" variant="outline" size="sm" wire:navigate>
                    {{ __('Billing') }}
                </x-slate::button>
            </div>
        </div>

        @if ($studioEnabled)
            <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-3">
                <h2 class="text-sm font-medium text-muted-foreground">{{ __('Sample product') }}</h2>
                <p class="text-lg font-semibold tracking-tight">{{ __('Studio') }}</p>
                <p class="text-sm text-muted-foreground">
                    {{ __('Clients, projects, and tasks are a replaceable micro-SaaS demo. Turn off with ELECTRIK_SAMPLE_PROJECTS=false.') }}
                </p>
                <div class="flex flex-wrap gap-2 pt-1">
                    <x-slate::button as="a" href="{{ route('clients.index') }}" size="sm" wire:navigate>{{ __('Clients') }}</x-slate::button>
                    <x-slate::button as="a" href="{{ route('projects.index') }}" variant="outline" size="sm" wire:navigate>{{ __('Projects') }}</x-slate::button>
                </div>
            </div>
        @elseif ($team && $canManageMembers)
            <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-3">
                <h2 class="text-sm font-medium text-muted-foreground">{{ __('Invitations') }}</h2>
                <p class="text-lg font-semibold tracking-tight">
                    {{ trans_choice('{0} No pending invites|{1} :count pending invite|[2,*] :count pending invites', $pendingInvites, ['count' => $pendingInvites]) }}
                </p>
                <x-slate::button as="a" href="{{ route('teams.members.invite', $team) }}" size="sm" wire:navigate>
                    {{ __('Invite member') }}
                </x-slate::button>
            </div>
        @endif
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        @if ($studioEnabled)
            <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-4">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="text-sm font-medium text-muted-foreground">{{ __('Recent projects') }}</h2>
                    <x-slate::button as="a" href="{{ route('projects.index') }}" variant="outline" size="sm" wire:navigate>
                        {{ __('All projects') }}
                    </x-slate::button>
                </div>
                <div class="space-y-3">
                    @forelse ($recentProjects as $project)
                        <a href="{{ route('projects.show', $project) }}" class="flex items-start justify-between gap-3 border-b border-border/60 pb-3 last:border-0 last:pb-0 hover:opacity-90" wire:navigate wire:key="dash-project-{{ $project->id }}">
                            <div class="min-w-0">
                                <p class="truncate font-medium">{{ $project->name }}</p>
                                <p class="text-xs text-muted-foreground">
                                    {{ $project->client?->name ?? __('Internal') }}
                                    · {{ trans_choice('{0} No open tasks|{1} :count open|[2,*] :count open', $project->open_tasks_count, ['count' => $project->open_tasks_count]) }}
                                </p>
                            </div>
                            <x-slate::badge variant="secondary">{{ $project->statusLabel() }}</x-slate::badge>
                        </a>
                    @empty
                        <p class="text-sm text-muted-foreground">{{ __('No projects yet.') }}</p>
                    @endforelse
                </div>
            </div>
        @endif

        <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-4 {{ $studioEnabled ? '' : 'lg:col-span-2' }}">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-sm font-medium text-muted-foreground">{{ __('Recent activity') }}</h2>
                @if ($team)
                    <x-slate::button as="a" href="{{ route('teams.activity', $team) }}" variant="outline" size="sm" wire:navigate>
                        {{ __('Full log') }}
                    </x-slate::button>
                @endif
            </div>
            <div class="space-y-3">
                @forelse ($recentActivity as $entry)
                    <div class="border-b border-border/60 pb-3 last:border-0 last:pb-0">
                        <p class="font-medium">{{ $entry->descriptionLabel() }}</p>
                        <p class="text-xs text-muted-foreground">
                            {{ $entry->causer?->name ?? __('System') }}
                            · {{ $entry->created_at?->diffForHumans() }}
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-muted-foreground">{{ __('No activity yet for this team.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
