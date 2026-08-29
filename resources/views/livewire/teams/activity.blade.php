<div class="space-y-6">
    <x-electrik::page-header title="{{ __('Activity') }}" :description="$team->name">
        <x-slot:actions>
            <x-slate::select wire:model.live="actionFilter" class="h-9 w-auto min-w-44">
                <option value="">{{ __('All actions') }}</option>
                @foreach ($actions as $action)
                    <option value="{{ $action }}">{{ $action }}</option>
                @endforeach
            </x-slate::select>
        </x-slot:actions>
    </x-electrik::page-header>

    <div class="space-y-2">
        @forelse ($entries as $entry)
            <div
                class="flex flex-wrap items-start justify-between gap-3 rounded-xl border border-border/80 bg-card px-4 py-3.5"
                wire:key="activity-{{ $entry->id }}"
            >
                <div class="min-w-0 space-y-1">
                    <p class="font-medium tracking-tight">{{ $entry->descriptionLabel() }}</p>
                    <p class="text-xs text-muted-foreground">
                        {{ $entry->causer?->name ?? __('System') }}
                        · {{ $entry->created_at?->diffForHumans() }}
                    </p>
                </div>
            </div>
        @empty
            <p class="text-sm text-muted-foreground">{{ __('No activity yet.') }}</p>
        @endforelse
    </div>

    <div>
        {{ $entries->links() }}
    </div>
</div>
