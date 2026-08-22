<div class="space-y-6">

    <x-electrik::page-header
        title="Billing"
        :description="'Subscription for '.$team->name.'.'"
    />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif
    @if (session('error'))
        <x-slate::alert variant="destructive" :title="session('error')" />
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <div class="space-y-3 rounded-xl border border-border/80 bg-card p-5 shadow-xs">
            <h2 class="text-sm font-medium text-muted-foreground">Current subscription</h2>
            @if ($subscription && $plan)
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-muted-foreground">Plan</dt>
                        <dd class="font-medium">{{ $plan->name }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-muted-foreground">Price</dt>
                        <dd class="font-medium">{{ $plan->formatted_price }}/{{ $plan->interval }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-muted-foreground">Status</dt>
                        <dd><x-slate::badge variant="secondary">{{ $subscription->stripe_status }}</x-slate::badge></dd>
                    </div>
                    @if ($subscription->ends_at)
                        <div class="flex justify-between gap-4">
                            <dt class="text-muted-foreground">Ends</dt>
                            <dd>{{ $subscription->ends_at->toFormattedDateString() }}</dd>
                        </div>
                    @endif
                </dl>
            @else
                <p class="text-sm text-muted-foreground">No active subscription.</p>
                <x-slate::button as="a" href="{{ route('billing.plans') }}" wire:navigate>View plans</x-slate::button>
            @endif
        </div>

        <div class="space-y-3 rounded-xl border border-border/80 bg-card p-5 shadow-xs">
            <h2 class="text-sm font-medium text-muted-foreground">Quick links</h2>
            <div class="flex flex-col gap-2">
                <x-slate::button as="a" href="{{ route('billing.plans') }}" variant="outline" wire:navigate>Change plan</x-slate::button>
                <x-slate::button as="a" href="{{ route('billing.payment-methods') }}" variant="outline" wire:navigate>Payment methods</x-slate::button>
                <x-slate::button as="a" href="{{ route('billing.invoices') }}" variant="outline" wire:navigate>Invoices</x-slate::button>
            </div>
        </div>
    </div>
</div>
