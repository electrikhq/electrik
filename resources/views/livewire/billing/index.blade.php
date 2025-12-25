<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Billing Overview</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-1">Manage your subscription and billing</p>
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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-slate::card>
            <h3 class="text-lg font-semibold mb-4">Current Subscription</h3>
            @if($subscription && $plan)
                <div class="space-y-2">
                    <div>
                        <span class="text-sm text-neutral-600 dark:text-neutral-400">Plan:</span>
                        <span class="font-medium ml-2">{{ $plan->name }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-neutral-600 dark:text-neutral-400">Price:</span>
                        <span class="font-medium ml-2">{{ $plan->formatted_price }}/{{ $plan->interval }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-neutral-600 dark:text-neutral-400">Status:</span>
                        <x-slate::badge 
                            :color="$subscription->active() ? 'success' : 'warning'"
                            class="ml-2"
                        >
                            {{ ucfirst($subscription->stripe_status) }}
                        </x-slate::badge>
                    </div>
                    @if($subscription->ends_at)
                        <div>
                            <span class="text-sm text-neutral-600 dark:text-neutral-400">Ends:</span>
                            <span class="font-medium ml-2">{{ $subscription->ends_at->format('M d, Y') }}</span>
                        </div>
                    @endif
                </div>
            @else
                <p class="text-neutral-600 dark:text-neutral-400">No active subscription</p>
                <a href="{{ route('billing.plans') }}" class="mt-4 inline-block">
                    <x-slate::button color="primary">View Plans</x-slate::button>
                </a>
            @endif
        </x-slate::card>

        <x-slate::card>
            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('billing.plans') }}">
                    <x-slate::button variant="outline" fullWidth>Change Plan</x-slate::button>
                </a>
                <a href="{{ route('billing.payment-methods') }}">
                    <x-slate::button variant="outline" fullWidth>Manage Payment Methods</x-slate::button>
                </a>
                <a href="{{ route('billing.invoices') }}">
                    <x-slate::button variant="outline" fullWidth>View Invoices</x-slate::button>
                </a>
            </div>
        </x-slate::card>
    </div>
</div>

