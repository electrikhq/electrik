<div class="mx-auto max-w-4xl space-y-8 py-8">
    <div class="text-center">
        <h1 class="text-3xl font-semibold tracking-tight">{{ __('Pricing') }}</h1>
        <p class="mt-2 text-muted-foreground">{{ __('Simple plans for teams of every size.') }}</p>
    </div>

    @forelse ($plans as $productName => $productPlans)
        <div class="space-y-3">
            @if ($plans->count() > 1)
                <h2 class="text-sm font-medium uppercase tracking-wider text-muted-foreground">{{ $productName }}</h2>
            @endif
            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($productPlans as $plan)
                    <x-slate::card class="border-border/80 shadow-xs" wire:key="plan-{{ $plan->id }}">
                        <x-slate::card-header>
                            <x-slate::card-title>{{ $plan->name }}</x-slate::card-title>
                            <x-slate::card-description>
                                {{ $plan->formatted_price }}/{{ $plan->interval }}
                                @if ($plan->seat_billing)
                                    · {{ __('per seat') }}
                                @endif
                            </x-slate::card-description>
                        </x-slate::card-header>
                        <x-slate::card-content>
                            @auth
                                <x-slate::button as="a" href="{{ route('billing.plans') }}" wire:navigate>
                                    {{ __('Choose plan') }}
                                </x-slate::button>
                            @else
                                <x-slate::button as="a" href="{{ route('register') }}" wire:navigate>
                                    {{ __('Get started') }}
                                </x-slate::button>
                            @endauth
                        </x-slate::card-content>
                    </x-slate::card>
                @endforeach
            </div>
        </div>
    @empty
        <p class="text-center text-sm text-muted-foreground">{{ __('Plans will appear here after Stripe sync.') }}</p>
    @endforelse
</div>
