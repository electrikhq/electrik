<div class="space-y-6">
    <x-electrik::page-header
        title="{{ __('Payment methods') }}"
        :description="__('Cards on file for :team. Add via Stripe Customer Portal.', ['team' => $team->name])"
    />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif
    @if (session('error'))
        <x-slate::alert variant="destructive" :title="session('error')" />
    @endif

    <div class="space-y-3">
        @forelse ($paymentMethods as $paymentMethod)
            <div class="flex items-center justify-between gap-3 rounded-xl border border-border/80 bg-card px-4 py-3.5 transition-colors hover:bg-accent/30" wire:key="pm-{{ $paymentMethod->id }}">
                <div>
                    <p class="font-medium">
                        {{ strtoupper($paymentMethod->card->brand) }} ···· {{ $paymentMethod->card->last4 }}
                    </p>
                    <p class="text-sm text-muted-foreground">
                        {{ __('Expires :month/:year', ['month' => $paymentMethod->card->exp_month, 'year' => $paymentMethod->card->exp_year]) }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    @if ($defaultPaymentMethod && $defaultPaymentMethod->id === $paymentMethod->id)
                        <x-slate::badge>{{ __('Default') }}</x-slate::badge>
                    @else
                        <x-slate::button type="button" variant="ghost" size="sm" wire:click="setDefault('{{ $paymentMethod->id }}')">
                            {{ __('Make default') }}
                        </x-slate::button>
                    @endif
                    <x-electrik::confirm
                        :title="__('Remove this payment method?')"
                        :description="__('You can add another card later via the Stripe Customer Portal.')"
                        :confirm-label="__('Remove')"
                        wire-click="remove('{{ $paymentMethod->id }}')"
                    >
                        <x-slate::button type="button" variant="ghost" size="sm">
                            {{ __('Remove') }}
                        </x-slate::button>
                    </x-electrik::confirm>
                </div>
            </div>
        @empty
            <p class="text-sm text-muted-foreground">{{ __('No payment methods yet.') }}</p>
        @endforelse
    </div>

    <x-slate::button type="button" wire:click="openPortal">{{ __('Manage in Stripe Portal') }}</x-slate::button>
</div>
