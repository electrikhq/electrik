<div class="mx-auto max-w-2xl space-y-6">
    <x-electrik::page-header
        title="{{ __('Outbound webhooks') }}"
        :description="$team->name"
    />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-header>
            <x-slate::card-title>{{ __('Add endpoint') }}</x-slate::card-title>
            <x-slate::card-description>
                {{ __('Electrik POSTs signed JSON when team events fire. Use * for all events.') }}
            </x-slate::card-description>
        </x-slate::card-header>
        <x-slate::card-content>
            <x-slate::form wire:submit="create" class="space-y-4">
                <x-slate::input wire:model="url" type="url" label="{{ __('URL') }}" placeholder="https://example.com/hooks/electrik" required />
                <x-slate::input wire:model="eventsText" label="{{ __('Events') }}" description="{{ __('Comma-separated, e.g. team.archived, member.joined, or *') }}" />
                <x-slate::button type="submit">{{ __('Create webhook') }}</x-slate::button>
            </x-slate::form>
        </x-slate::card-content>
    </x-slate::card>

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-header>
            <x-slate::card-title>{{ __('Endpoints') }}</x-slate::card-title>
        </x-slate::card-header>
        <x-slate::card-content class="space-y-3">
            @forelse ($webhooks as $webhook)
                <div class="rounded-xl border border-border/80 px-4 py-3 space-y-2" wire:key="wh-{{ $webhook->id }}">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate font-medium">{{ $webhook->url }}</p>
                            <p class="text-xs text-muted-foreground">
                                {{ implode(', ', $webhook->events ?? ['*']) }}
                                @if ($webhook->last_triggered_at)
                                    · {{ __('Last: :time', ['time' => $webhook->last_triggered_at->diffForHumans()]) }}
                                @endif
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <x-slate::badge :variant="$webhook->enabled ? 'default' : 'secondary'">
                                {{ $webhook->enabled ? __('Enabled') : __('Disabled') }}
                            </x-slate::badge>
                            <x-slate::button type="button" size="sm" variant="outline" wire:click="toggle({{ $webhook->id }})">
                                {{ $webhook->enabled ? __('Disable') : __('Enable') }}
                            </x-slate::button>
                            <x-electrik::confirm
                                :title="__('Delete webhook?')"
                                :description="__('Deliveries for this endpoint will stop.')"
                                :confirm-label="__('Delete')"
                                wire-click="delete({{ $webhook->id }})"
                            >
                                <x-slate::button type="button" size="sm" variant="destructive">{{ __('Delete') }}</x-slate::button>
                            </x-electrik::confirm>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-muted-foreground">{{ __('No webhook endpoints yet.') }}</p>
            @endforelse
        </x-slate::card-content>
    </x-slate::card>
</div>
