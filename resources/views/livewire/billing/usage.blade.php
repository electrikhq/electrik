<div class="mx-auto max-w-lg space-y-6">
    <x-electrik::page-header
        title="{{ __('Usage') }}"
        description="{{ __('Report metered usage to Stripe for attached prices.') }}"
    />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif
    @if (session('error'))
        <x-slate::alert variant="destructive" :title="session('error')" />
    @endif

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-content>
            @if ($meteredPlans->isEmpty())
                <p class="text-sm text-muted-foreground">
                    {{ __('No metered plans yet. Mark a Stripe price as metered in Electrik (stripe_plans.metered) after sync.') }}
                </p>
            @else
                <x-slate::form wire:submit="report" class="space-y-4">
                    <x-slate::select wire:model="planId" label="{{ __('Metered price') }}" required>
                        <option value="">{{ __('Select…') }}</option>
                        @foreach ($meteredPlans as $plan)
                            <option value="{{ $plan->id }}">{{ $plan->name }} ({{ $plan->stripe_price_id }})</option>
                        @endforeach
                    </x-slate::select>
                    <x-slate::input wire:model="quantity" type="number" min="1" label="{{ __('Quantity') }}" required />
                    <x-slate::button type="submit">{{ __('Report usage') }}</x-slate::button>
                </x-slate::form>
            @endif
        </x-slate::card-content>
    </x-slate::card>
</div>
