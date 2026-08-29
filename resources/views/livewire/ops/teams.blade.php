<div class="space-y-6">
    <x-electrik::page-header
        title="{{ __('Teams') }}"
        description="{{ __('Every team on the platform and its billing status.') }}"
    />

    <x-slate::input
        wire:model.live.debounce.400ms="search"
        type="search"
        placeholder="{{ __('Search by team name') }}"
        class="max-w-sm"
    />

    <div class="space-y-2">
        @forelse ($teams as $team)
            <div
                class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-border/80 bg-card px-4 py-3.5"
                wire:key="ops-team-{{ $team->id }}"
            >
                <div class="min-w-0">
                    <p class="font-medium tracking-tight">{{ $team->name }}</p>
                    <p class="text-xs text-muted-foreground">
                        {{ trans_choice('{1} :count member|[2,*] :count members', $team->users_count, ['count' => $team->users_count]) }}
                        @if ($team->electrik_owner_name)
                            · {{ __('Owner :name', ['name' => $team->electrik_owner_name]) }}
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    @if ($team->electrik_past_due)
                        <x-slate::badge variant="destructive">{{ $team->electrik_billing_label }}</x-slate::badge>
                    @else
                        <x-slate::badge variant="secondary">{{ $team->electrik_billing_label }}</x-slate::badge>
                    @endif
                </div>
            </div>
        @empty
            <x-slate::alert variant="info" :title="__('No teams found')" />
        @endforelse
    </div>

    <div>
        {{ $teams->links() }}
    </div>
</div>
