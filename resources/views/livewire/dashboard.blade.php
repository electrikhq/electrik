<div class="space-y-8">
    <x-electrik::page-header
        title="Dashboard"
        :description="'Welcome'.($user?->name ? ', '.$user->name : '').'. Here’s your workspace at a glance.'"
    />

    @if (request()->boolean('verified'))
        <x-slate::alert variant="success" title="Email verified" description="Your email address is confirmed." />
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-3">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-sm font-medium text-muted-foreground">Workspace</h2>
                @if ($team)
                    <x-slate::badge variant="secondary">Current</x-slate::badge>
                @endif
            </div>
            @if ($team)
                <p class="text-lg font-semibold tracking-tight">{{ $team->name }}</p>
                <div class="flex flex-wrap gap-2 pt-1">
                    <x-slate::button as="a" href="{{ route('teams.index') }}" variant="outline" size="sm" wire:navigate>Teams</x-slate::button>
                    <x-slate::button as="a" href="{{ route('teams.members', $team) }}" variant="outline" size="sm" wire:navigate>Members</x-slate::button>
                </div>
            @else
                <p class="text-sm text-muted-foreground">No team selected.</p>
                <x-slate::button as="a" href="{{ route('teams.create') }}" size="sm" wire:navigate>Create team</x-slate::button>
            @endif
        </div>

        <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-3">
            <h2 class="text-sm font-medium text-muted-foreground">Billing</h2>
            @if ($subscription?->active())
                <p class="text-lg font-semibold tracking-tight capitalize">{{ str_replace('_', ' ', $subscription->stripe_status) }}</p>
                <p class="text-sm text-muted-foreground">Subscription is active for this team.</p>
            @else
                <p class="text-lg font-semibold tracking-tight">No plan</p>
                <p class="text-sm text-muted-foreground">Choose a plan when you’re ready to upgrade.</p>
            @endif
            <div class="pt-1">
                <x-slate::button as="a" href="{{ route('billing.index') }}" variant="outline" size="sm" wire:navigate>Billing</x-slate::button>
            </div>
        </div>
    </div>
</div>
