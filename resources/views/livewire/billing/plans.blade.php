<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Subscription Plans</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-1">Choose the plan that's right for you</p>
    </div>

    @if(session('message'))
        <x-slate::alert type="success" class="mb-6">
            {{ session('message') }}
        </x-slate::alert>
    @endif

    @if(session('error'))
        <x-slate::alert type="error" class="mb-6">
            {{ session('error') }}
        </x-slate::alert>
    @endif

    @forelse($plans as $productName => $productPlans)
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">{{ $productName }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($productPlans as $plan)
                    <x-slate::card class="{{ $currentPlan && $currentPlan->id === $plan->id ? 'ring-2 ring-primary-500' : '' }}">
                        <div class="text-center mb-4">
                            <h3 class="text-lg font-semibold">{{ $plan->name }}</h3>
                            <div class="mt-2">
                                <span class="text-3xl font-bold">{{ $plan->formatted_price }}</span>
                                <span class="text-neutral-600 dark:text-neutral-400">/{{ $plan->interval }}</span>
                            </div>
                        </div>
                        
                        @if($currentPlan && $currentPlan->id === $plan->id)
                            <x-slate::badge color="primary" class="w-full justify-center mb-4">Current Plan</x-slate::badge>
                        @else
                            <a href="{{ route('billing.subscription') }}" class="block">
                                <x-slate::button color="primary" fullWidth>Select Plan</x-slate::button>
                            </a>
                        @endif
                    </x-slate::card>
                @endforeach
            </div>
        </div>
    @empty
        <x-slate::card>
            <div class="text-center py-12">
                <p class="text-neutral-600 dark:text-neutral-400 mb-4">No plans available.</p>
                <p class="text-sm text-neutral-500 dark:text-neutral-500">
                    Run <code class="bg-neutral-100 dark:bg-neutral-800 px-2 py-1 rounded">php artisan electrik:stripe:sync</code> to sync plans from Stripe.
                </p>
            </div>
        </x-slate::card>
    @endforelse
</div>

