<div class="space-y-6">
    <x-electrik::page-header
        title="{{ __('Webhooks') }}"
        description="{{ __('Recent Stripe webhook events received by the app.') }}"
    />

    <div class="space-y-2">
        @forelse ($events as $event)
            <div
                class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-border/80 bg-card px-4 py-3.5"
                wire:key="webhook-{{ $event->id }}"
            >
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="font-medium tracking-tight">{{ $event->type }}</p>
                        @if ($event->status === 'failed')
                            <x-slate::badge variant="destructive">{{ $event->status }}</x-slate::badge>
                        @elseif ($event->status === 'processed')
                            <x-slate::badge variant="secondary">{{ $event->status }}</x-slate::badge>
                        @else
                            <x-slate::badge variant="outline">{{ $event->status }}</x-slate::badge>
                        @endif
                    </div>
                    <p class="text-xs text-muted-foreground">
                        {{ $event->team?->name ?? __('Unknown team') }}
                        @if ($event->customer_id)
                            · {{ $event->customer_id }}
                        @endif
                        · {{ $event->created_at?->diffForHumans() }}
                    </p>
                </div>
                @if ($event->processed_at)
                    <p class="text-xs text-muted-foreground">
                        {{ __('Processed :time', ['time' => $event->processed_at->diffForHumans()]) }}
                    </p>
                @endif
            </div>
        @empty
            <x-slate::alert variant="info" :title="__('No webhook events yet')" />
        @endforelse
    </div>

    <div>
        {{ $events->links() }}
    </div>
</div>
