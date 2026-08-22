<div class="space-y-6">
    <x-electrik::page-header title="Activity" :description="$team->name" />

    <div class="space-y-2">
        @forelse ($entries as $entry)
            <div
                class="flex flex-wrap items-start justify-between gap-3 rounded-xl border border-border/80 bg-card px-4 py-3.5"
                wire:key="activity-{{ $entry->id }}"
            >
                <div class="min-w-0 space-y-1">
                    <p class="font-medium tracking-tight">{{ $entry->description() }}</p>
                    <p class="text-xs text-muted-foreground">
                        {{ $entry->user?->name ?? __('System') }}
                        · {{ $entry->created_at?->diffForHumans() }}
                    </p>
                </div>
            </div>
        @empty
            <p class="text-sm text-muted-foreground">{{ __('No activity yet.') }}</p>
        @endforelse
    </div>
</div>
