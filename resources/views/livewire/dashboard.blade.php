<div class="space-y-8">
    <x-electrik::page-header
        title="Dashboard"
        :description="'Welcome'.($user?->name ? ', '.$user->name : '').'. Here’s your workspace at a glance.'"
    />

    @if (request()->boolean('verified'))
        <x-slate::alert variant="success" title="Email verified" description="Your email address is confirmed." />
    @endif

    @if ($needsSubscription)
        <x-slate::alert variant="destructive" :title="__('Subscription required')">
            <p class="text-sm">
                {{ __('Choose a plan to unlock the full app.') }}
                <a href="{{ route('billing.plans') }}" class="font-medium underline underline-offset-4" wire:navigate>{{ __('View plans') }}</a>
            </p>
        </x-slate::alert>
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
                <p class="text-sm text-muted-foreground">{{ __('Choose a plan when you’re ready to upgrade.') }}</p>
            @endif
            <div class="pt-1">
                <x-slate::button as="a" href="{{ route('billing.index') }}" variant="outline" size="sm" wire:navigate>
                    {{ __('Billing') }}
                </x-slate::button>
            </div>
        </div>

        @if ($team && $canManageMembers)
            <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-3">
                <h2 class="text-sm font-medium text-muted-foreground">{{ __('Invitations') }}</h2>
                <p class="text-lg font-semibold tracking-tight">
                    {{ trans_choice('{0} No pending invites|{1} :count pending invite|[2,*] :count pending invites', $pendingInvites, ['count' => $pendingInvites]) }}
                </p>
                <div class="flex flex-wrap gap-2 pt-1">
                    <x-slate::button as="a" href="{{ route('teams.members.invite', $team) }}" size="sm" wire:navigate>
                        {{ __('Invite member') }}
                    </x-slate::button>
                    <x-slate::button as="a" href="{{ route('teams.members', $team) }}" variant="outline" size="sm" wire:navigate>
                        {{ __('View members') }}
                    </x-slate::button>
                </div>
            </div>
        @endif
    </div>
</div>
