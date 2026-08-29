<div class="space-y-6">
    <x-electrik::page-header
        title="{{ __('Billing') }}"
        :description="__('Subscription for :team.', ['team' => $team->name])"
    />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif
    @if (session('error'))
        <x-slate::alert variant="destructive" :title="session('error')" />
    @endif

    @if ($trialLabel)
        <x-slate::alert variant="default" :title="$trialLabel" />
    @endif

    @if ($pastDue)
        <x-slate::alert
            variant="destructive"
            :title="__('Payment failed')"
            :description="__('Your subscription is past due. Update your card to avoid losing access.')"
        >
            <div class="mt-3">
                <x-slate::button as="a" href="{{ route('billing.payment-methods') }}" size="sm" wire:navigate>
                    {{ __('Update payment method') }}
                </x-slate::button>
            </div>
        </x-slate::alert>
    @endif

    @if (! $healthOk)
        <x-slate::card class="border-amber-500/30 shadow-xs">
            <x-slate::card-header>
                <x-slate::card-title>{{ __('Billing setup') }}</x-slate::card-title>
                <x-slate::card-description>
                    {{ __('Complete Stripe configuration so subscriptions and webhooks work in production.') }}
                </x-slate::card-description>
            </x-slate::card-header>
            <x-slate::card-content>
                <ul class="space-y-2 text-sm">
                    @foreach ($healthChecks as $check)
                        <li class="flex flex-wrap items-start justify-between gap-2">
                            <span @class(['text-muted-foreground' => ! $check['ok']])>{{ $check['label'] }}</span>
                            @if ($check['ok'])
                                <x-slate::badge variant="secondary">{{ __('OK') }}</x-slate::badge>
                            @else
                                <span class="text-muted-foreground">{{ $check['hint'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </x-slate::card-content>
        </x-slate::card>
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <div class="space-y-3 rounded-xl border border-border/80 bg-card p-5 shadow-xs">
            <h2 class="text-sm font-medium text-muted-foreground">{{ __('Current subscription') }}</h2>
            @if ($subscription && $plan)
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-muted-foreground">{{ __('Plan') }}</dt>
                        <dd class="font-medium">{{ $plan->name }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-muted-foreground">{{ __('Price') }}</dt>
                        <dd class="font-medium">{{ $plan->formatted_price }}/{{ $plan->interval }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-muted-foreground">{{ __('Status') }}</dt>
                        <dd><x-slate::badge variant="secondary">{{ $statusLabel }}</x-slate::badge></dd>
                    </div>
                    @if ($subscription->trial_ends_at && $subscription->onTrial())
                        <div class="flex justify-between gap-4">
                            <dt class="text-muted-foreground">{{ __('Trial ends') }}</dt>
                            <dd>{{ $subscription->trial_ends_at->toFormattedDateString() }}</dd>
                        </div>
                    @endif
                    @if ($subscription->ends_at)
                        <div class="flex justify-between gap-4">
                            <dt class="text-muted-foreground">{{ __('Ends') }}</dt>
                            <dd>{{ $subscription->ends_at->toFormattedDateString() }}</dd>
                        </div>
                    @endif
                </dl>
            @else
                <p class="text-sm text-muted-foreground">{{ __('No active subscription.') }}</p>
                <x-slate::button as="a" href="{{ route('billing.plans') }}" wire:navigate>{{ __('View plans') }}</x-slate::button>
            @endif
        </div>

        <div class="space-y-3 rounded-xl border border-border/80 bg-card p-5 shadow-xs">
            <h2 class="text-sm font-medium text-muted-foreground">{{ __('Quick links') }}</h2>
            <div class="flex flex-col gap-2">
                <x-slate::button as="a" href="{{ route('billing.plans') }}" variant="outline" wire:navigate>{{ __('Change plan') }}</x-slate::button>
                <x-slate::button as="a" href="{{ route('billing.payment-methods') }}" variant="outline" wire:navigate>{{ __('Payment methods') }}</x-slate::button>
                <x-slate::button as="a" href="{{ route('billing.invoices') }}" variant="outline" wire:navigate>{{ __('Invoices') }}</x-slate::button>
            </div>
        </div>
    </div>

    @if ($webhookEvents->isNotEmpty())
        <x-slate::card class="border-border/80 shadow-xs">
            <x-slate::card-header>
                <x-slate::card-title>{{ __('Recent Stripe events') }}</x-slate::card-title>
                <x-slate::card-description>{{ __('Webhook activity related to this team.') }}</x-slate::card-description>
            </x-slate::card-header>
            <x-slate::card-content class="space-y-2">
                @foreach ($webhookEvents as $event)
                    <div class="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-border/80 px-3 py-2 text-sm" wire:key="wh-{{ $event->id }}">
                        <div class="min-w-0">
                            <p class="font-medium truncate">{{ $event->type }}</p>
                            <p class="text-xs text-muted-foreground">{{ $event->created_at?->diffForHumans() }}</p>
                        </div>
                        <x-slate::badge variant="secondary">{{ $event->status }}</x-slate::badge>
                    </div>
                @endforeach
            </x-slate::card-content>
        </x-slate::card>
    @endif
</div>
