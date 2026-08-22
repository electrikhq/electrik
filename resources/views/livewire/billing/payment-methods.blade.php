<div class="space-y-6">

    <x-electrik::page-header
        title="Payment methods"
        :description="'Cards on file for '.$team->name.'. Add via Stripe Customer Portal.'"
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
                        Expires {{ $paymentMethod->card->exp_month }}/{{ $paymentMethod->card->exp_year }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    @if ($defaultPaymentMethod && $defaultPaymentMethod->id === $paymentMethod->id)
                        <x-slate::badge>Default</x-slate::badge>
                    @else
                        <x-slate::button type="button" variant="ghost" size="sm" wire:click="setDefault('{{ $paymentMethod->id }}')">
                            Make default
                        </x-slate::button>
                    @endif
                    <x-electrik::confirm
                        title="Remove this payment method?"
                        description="You can add another card later via the Stripe Customer Portal."
                        confirm-label="Remove"
                        wire-click="remove('{{ $paymentMethod->id }}')"
                    >
                        <x-slate::button type="button" variant="ghost" size="sm">
                            Remove
                        </x-slate::button>
                    </x-electrik::confirm>
                </div>
            </div>
        @empty
            <p class="text-sm text-muted-foreground">No payment methods yet.</p>
        @endforelse
    </div>

    <x-slate::button type="button" wire:click="openPortal">Manage in Stripe Portal</x-slate::button>
</div>
