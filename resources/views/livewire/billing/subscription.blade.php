<div class="space-y-6">

    <x-electrik::page-header
        title="{{ __('Subscription') }}"
        description="{{ __('Manage the current team subscription.') }}"
    />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif
    @if (session('error'))
        <x-slate::alert variant="destructive" :title="session('error')" />
    @endif

    <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs">
        @if ($subscription && $plan)
            <dl class="grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm text-muted-foreground">{{ __('Plan') }}</dt>
                    <dd class="font-medium">{{ $plan->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted-foreground">{{ __('Price') }}</dt>
                    <dd class="font-medium">{{ $plan->formatted_price }}/{{ $plan->interval }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted-foreground">{{ __('Status') }}</dt>
                    <dd><x-slate::badge variant="secondary">{{ $subscription->stripe_status }}</x-slate::badge></dd>
                </div>
                @if ($subscription->ends_at)
                    <div>
                        <dt class="text-sm text-muted-foreground">{{ __('Ends') }}</dt>
                        <dd class="font-medium">{{ $subscription->ends_at->toFormattedDateString() }}</dd>
                    </div>
                @endif
            </dl>

            <div class="mt-6 flex flex-wrap gap-2 border-t border-border pt-4">
                @if ($subscription->onGracePeriod())
                    <x-slate::button type="button" wire:click="resume">{{ __('Resume') }}</x-slate::button>
                @elseif ($subscription->active())
                    <x-electrik::confirm
                        :title="__('Cancel subscription?')"
                        :description="__('Access continues until the end of the current billing period.')"
                        :confirm-label="__('Cancel subscription')"
                        confirm-variant="outline"
                        wire-click="cancel"
                    >
                        <x-slate::button type="button" variant="outline">
                            {{ __('Cancel subscription') }}
                        </x-slate::button>
                    </x-electrik::confirm>
                @endif
                <x-slate::button as="a" href="{{ route('billing.plans') }}" variant="ghost" wire:navigate>{{ __('Change plan') }}</x-slate::button>
            </div>
        @else
            <p class="text-sm text-muted-foreground">{{ __('No active subscription.') }}</p>
            <div class="mt-4">
                <x-slate::button as="a" href="{{ route('billing.plans') }}" wire:navigate>{{ __('View plans') }}</x-slate::button>
            </div>
        @endif
    </div>
</div>
