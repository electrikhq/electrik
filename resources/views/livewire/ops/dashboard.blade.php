<div class="space-y-8">
    <x-electrik::page-header
        title="{{ __('Operations') }}"
        description="{{ __('Account health across the whole app.') }}"
    />

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('ops.users') }}" wire:navigate class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-2 transition-colors hover:bg-accent/30">
            <h2 class="text-sm font-medium text-muted-foreground">{{ __('Users') }}</h2>
            <p class="text-2xl font-semibold tracking-tight">{{ number_format($userCount) }}</p>
        </a>

        <a href="{{ route('ops.teams') }}" wire:navigate class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-2 transition-colors hover:bg-accent/30">
            <h2 class="text-sm font-medium text-muted-foreground">{{ __('Teams') }}</h2>
            <p class="text-2xl font-semibold tracking-tight">{{ number_format($teamCount) }}</p>
        </a>

        <a href="{{ route('ops.teams') }}" wire:navigate class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-2 transition-colors hover:bg-accent/30">
            <h2 class="text-sm font-medium text-muted-foreground">{{ __('Past due') }}</h2>
            <p class="text-2xl font-semibold tracking-tight">{{ number_format($pastDueCount) }}</p>
        </a>

        <a href="{{ route('ops.failed-jobs') }}" wire:navigate class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-2 transition-colors hover:bg-accent/30">
            <h2 class="text-sm font-medium text-muted-foreground">{{ __('Failed jobs') }}</h2>
            <p class="text-2xl font-semibold tracking-tight">{{ number_format($failedJobCount) }}</p>
        </a>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-2">
            <h2 class="text-sm font-medium text-muted-foreground">{{ __('Signups (7d)') }}</h2>
            <p class="text-2xl font-semibold tracking-tight">{{ number_format($signups7) }}</p>
        </div>
        <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-2">
            <h2 class="text-sm font-medium text-muted-foreground">{{ __('Signups (30d)') }}</h2>
            <p class="text-2xl font-semibold tracking-tight">{{ number_format($signups30) }}</p>
        </div>
        <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-2">
            <h2 class="text-sm font-medium text-muted-foreground">{{ __('Active subscriptions') }}</h2>
            <p class="text-2xl font-semibold tracking-tight">{{ number_format($activeSubscriptions) }}</p>
        </div>
        <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs space-y-2">
            <h2 class="text-sm font-medium text-muted-foreground">{{ __('Est. MRR') }}</h2>
            <p class="text-2xl font-semibold tracking-tight">${{ number_format($mrrCents / 100, 2) }}</p>
            <p class="text-xs text-muted-foreground">{{ __('Local catalog stub, not Stripe Reporting.') }}</p>
        </div>
    </div>

    <div class="flex flex-wrap gap-2">
        <x-slate::button as="a" href="{{ route('ops.users') }}" variant="outline" size="sm" wire:navigate>{{ __('Manage users') }}</x-slate::button>
        <x-slate::button as="a" href="{{ route('ops.teams') }}" variant="outline" size="sm" wire:navigate>{{ __('View teams') }}</x-slate::button>
        <x-slate::button as="a" href="{{ route('ops.plans') }}" variant="outline" size="sm" wire:navigate>{{ __('Plan features') }}</x-slate::button>
        <x-slate::button as="a" href="{{ route('ops.webhooks') }}" variant="outline" size="sm" wire:navigate>{{ __('View webhooks') }}</x-slate::button>
        <x-slate::button as="a" href="{{ route('ops.failed-jobs') }}" variant="outline" size="sm" wire:navigate>{{ __('View failed jobs') }}</x-slate::button>
        <x-slate::button as="a" href="{{ route('ops.announcements.index') }}" variant="outline" size="sm" wire:navigate>{{ __('Announcements') }}</x-slate::button>
        <x-slate::button as="a" href="{{ route('ops.mail-preview') }}" variant="outline" size="sm" wire:navigate>{{ __('Email preview') }}</x-slate::button>
    </div>
</div>
