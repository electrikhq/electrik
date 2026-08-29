<div class="space-y-8">
    <x-electrik::page-header
        title="{{ __('Plan features') }}"
        description="{{ __('Edit feature flags, add-on, and metered flags on synced Stripe plans.') }}"
    />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif

    <div class="space-y-3">
        @forelse ($plans as $plan)
            <div class="rounded-xl border border-border/80 px-4 py-3" wire:key="ops-plan-{{ $plan->id }}">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="font-medium">{{ $plan->name }}</p>
                        <p class="text-xs text-muted-foreground">
                            {{ $plan->stripe_price_id }}
                            · {{ $plan->formatted_price }}/{{ $plan->interval }}
                            @if ($plan->is_addon) · {{ __('Add-on') }} @endif
                            @if ($plan->metered) · {{ __('Metered') }} @endif
                        </p>
                    </div>
                    <x-slate::button type="button" size="sm" variant="outline" wire:click="edit({{ $plan->id }})">
                        {{ __('Edit') }}
                    </x-slate::button>
                </div>

                @if ($editingId === $plan->id)
                    <x-slate::form wire:submit="save" class="mt-4 space-y-3">
                        <x-slate::textarea wire:model="featuresJson" label="{{ __('Features JSON') }}" rows="8" />
                        <x-slate::checkbox wire:model="isAddon" label="{{ __('Add-on') }}" />
                        <x-slate::checkbox wire:model="metered" label="{{ __('Metered usage') }}" />
                        <div class="flex gap-2">
                            <x-slate::button type="submit" size="sm">{{ __('Save') }}</x-slate::button>
                            <x-slate::button type="button" size="sm" variant="ghost" wire:click="$set('editingId', null)">{{ __('Cancel') }}</x-slate::button>
                        </div>
                    </x-slate::form>
                @endif
            </div>
        @empty
            <p class="text-sm text-muted-foreground">{{ __('No plans synced yet. Run electrik:stripe:sync.') }}</p>
        @endforelse
    </div>
</div>
