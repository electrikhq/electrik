<div class="space-y-6">

    <x-electrik::page-header
        title="{{ __('Plans') }}"
        :description="__('Choose a plan. Paid plans open Stripe Checkout.')"
    />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif
    @if (session('error'))
        <x-slate::alert variant="destructive" :title="session('error')" />
    @endif

    @forelse ($plans as $productName => $productPlans)
        <div class="space-y-3">
            <h2 class="text-lg font-medium">{{ $productName }}</h2>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($productPlans as $plan)
                    <div
                        wire:key="plan-{{ $plan->id }}"
                        @class([
                            'flex flex-col gap-4 rounded-xl border border-border/80 bg-card p-5 shadow-xs',
                            'ring-2 ring-foreground' => $currentPlan && (int) $currentPlan->id === (int) $plan->id,
                        ])
                    >
                        <div>
                            <p class="font-medium">{{ $plan->name }}</p>
                            <p class="mt-1 text-2xl font-semibold tracking-tight">
                                {{ $plan->formatted_price }}
                                <span class="text-sm font-normal text-muted-foreground">/ {{ $plan->interval }}</span>
                            </p>
                        </div>

                        @if ($currentPlan && (int) $currentPlan->id === (int) $plan->id)
                            <x-slate::badge>{{ __('Current plan') }}</x-slate::badge>
                        @else
                            <x-slate::button type="button" wire:click="subscribe({{ $plan->id }})" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="subscribe({{ $plan->id }})">
                                    {{ $plan->isFree() ? __('Start free') : __('Subscribe') }}
                                </span>
                                <span wire:loading wire:target="subscribe({{ $plan->id }})">{{ __('Working…') }}</span>
                            </x-slate::button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <x-slate::alert
            variant="info"
            :title="__('No plans yet')"
            :description="__('Create products/prices in Stripe (test mode), then run: php artisan electrik:stripe:sync')"
        />
    @endforelse

    @if ($addons->isNotEmpty())
        <div class="space-y-3">
            <h2 class="text-lg font-medium">{{ __('Add-ons') }}</h2>
            <p class="text-sm text-muted-foreground">{{ __('Extra products attached to your active subscription.') }}</p>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($addons as $plan)
                    <div wire:key="addon-{{ $plan->id }}" class="flex flex-col gap-4 rounded-xl border border-border/80 bg-card p-5 shadow-xs">
                        <div>
                            <p class="font-medium">{{ $plan->name }}</p>
                            <p class="mt-1 text-2xl font-semibold tracking-tight">
                                {{ $plan->formatted_price }}
                                <span class="text-sm font-normal text-muted-foreground">/ {{ $plan->interval }}</span>
                            </p>
                            @if ($plan->metered)
                                <x-slate::badge variant="secondary" class="mt-2">{{ __('Metered') }}</x-slate::badge>
                            @endif
                        </div>
                        @if ($hasActiveSubscription)
                            <x-slate::button type="button" wire:click="addAddon({{ $plan->id }})" wire:loading.attr="disabled">
                                {{ __('Add to subscription') }}
                            </x-slate::button>
                        @else
                            <p class="text-sm text-muted-foreground">{{ __('Subscribe to a base plan first.') }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
