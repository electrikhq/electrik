<div class="space-y-6">
    <x-electrik::page-header
        title="{{ __('Announcements') }}"
        description="{{ __('Broadcast updates to users as in-app notifications.') }}"
    >
        <x-slot:actions>
            <x-slate::button as="a" href="{{ route('ops.announcements.create') }}" wire:navigate>
                {{ __('New announcement') }}
            </x-slate::button>
        </x-slot:actions>
    </x-electrik::page-header>

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif

    <div class="space-y-2">
        @forelse ($announcements as $announcement)
            <div
                class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-border/80 bg-card px-4 py-3.5 transition-colors hover:bg-accent/30"
                wire:key="announcement-{{ $announcement->id }}"
            >
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="font-medium tracking-tight">{{ $announcement->title }}</p>
                        @if ($announcement->isActive())
                            <x-slate::badge>{{ __('Active') }}</x-slate::badge>
                        @else
                            <x-slate::badge variant="outline">{{ __('Inactive') }}</x-slate::badge>
                        @endif
                    </div>
                    <p class="text-xs text-muted-foreground">
                        {{ $announcement->audienceLabel() }}
                        @if ($announcement->starts_at)
                            · {{ __('From :date', ['date' => $announcement->starts_at->toFormattedDateString()]) }}
                        @endif
                        @if ($announcement->ends_at)
                            · {{ __('Until :date', ['date' => $announcement->ends_at->toFormattedDateString()]) }}
                        @endif
                    </p>
                </div>
                <div class="flex gap-2">
                    <x-electrik::confirm
                        :title="__('Send this announcement now?')"
                        :description="__('This notifies every matching user immediately.')"
                        :confirm-label="__('Send')"
                        confirm-variant="default"
                        wire-click="publish({{ $announcement->id }})"
                    >
                        <x-slate::button type="button" variant="outline" size="sm">
                            {{ __('Send now') }}
                        </x-slate::button>
                    </x-electrik::confirm>
                    <x-slate::button as="a" href="{{ route('ops.announcements.edit', $announcement) }}" variant="ghost" size="sm" wire:navigate>
                        {{ __('Edit') }}
                    </x-slate::button>
                    <x-electrik::confirm
                        :title="__('Delete this announcement?')"
                        :description="__('This cannot be undone.')"
                        :confirm-label="__('Delete')"
                        wire-click="delete({{ $announcement->id }})"
                    >
                        <x-slate::button type="button" variant="ghost" size="sm">
                            {{ __('Delete') }}
                        </x-slate::button>
                    </x-electrik::confirm>
                </div>
            </div>
        @empty
            <x-slate::alert
                variant="info"
                title="{{ __('No announcements yet') }}"
                description="{{ __('Create one to broadcast an update to your users.') }}"
            />
        @endforelse
    </div>
</div>
