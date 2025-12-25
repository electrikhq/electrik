<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Subscription Management</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-1">Manage your current subscription</p>
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

    <x-slate::card>
        @if($subscription && $plan)
            <h3 class="text-lg font-semibold mb-4">Current Subscription</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <dt class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Plan Name</dt>
                    <dd class="mt-1 font-medium">{{ $plan->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Price</dt>
                    <dd class="mt-1 font-medium">{{ $plan->formatted_price }}/{{ $plan->interval }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Status</dt>
                    <dd class="mt-1">
                        <x-slate::badge :color="$subscription->active() ? 'success' : 'warning'">
                            {{ ucfirst($subscription->stripe_status) }}
                        </x-slate::badge>
                    </dd>
                </div>
                @if($subscription->trial_ends_at)
                    <div>
                        <dt class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Trial Ends</dt>
                        <dd class="mt-1 font-medium">{{ $subscription->trial_ends_at->format('M d, Y') }}</dd>
                    </div>
                @endif
                @if($subscription->ends_at)
                    <div>
                        <dt class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Ends At</dt>
                        <dd class="mt-1 font-medium">{{ $subscription->ends_at->format('M d, Y') }}</dd>
                    </div>
                @endif
            </dl>

            @if($subscription->active() && !$subscription->canceled())
                <div class="mt-6 pt-6 border-t border-neutral-200 dark:border-neutral-700">
                    <form wire:submit="cancel">
                        <x-slate::button 
                            type="submit" 
                            variant="outline" 
                            color="error"
                            wire:confirm="Are you sure you want to cancel your subscription? This action cannot be undone."
                        >
                            Cancel Subscription
                        </x-slate::button>
                    </form>
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <p class="text-neutral-600 dark:text-neutral-400 mb-4">No active subscription.</p>
                <a href="{{ route('billing.plans') }}">
                    <x-slate::button color="primary">View Plans</x-slate::button>
                </a>
            </div>
        @endif
    </x-slate::card>
</div>

