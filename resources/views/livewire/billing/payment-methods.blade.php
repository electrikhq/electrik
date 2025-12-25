<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Payment Methods</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-1">Manage your payment methods</p>
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
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4">Saved Payment Methods</h3>
            
            @if($paymentMethods->count() > 0)
                <div class="space-y-4">
                    @foreach($paymentMethods as $paymentMethod)
                        <div class="flex items-center justify-between p-4 border border-neutral-200 dark:border-neutral-700 rounded-lg {{ $defaultPaymentMethod && $defaultPaymentMethod->id === $paymentMethod->id ? 'bg-primary-50 dark:bg-primary-900/20' : '' }}">
                            <div class="flex items-center space-x-4">
                                <div>
                                    <div class="font-medium">
                                        {{ $paymentMethod->card->brand }} •••• {{ $paymentMethod->card->last4 }}
                                    </div>
                                    <div class="text-sm text-neutral-600 dark:text-neutral-400">
                                        Expires {{ $paymentMethod->card->exp_month }}/{{ $paymentMethod->card->exp_year }}
                                    </div>
                                </div>
                                @if($defaultPaymentMethod && $defaultPaymentMethod->id === $paymentMethod->id)
                                    <x-slate::badge color="primary">Default</x-slate::badge>
                                @endif
                            </div>
                            <div class="flex gap-2">
                                @if(!$defaultPaymentMethod || $defaultPaymentMethod->id !== $paymentMethod->id)
                                    <button 
                                        wire:click="setDefaultPaymentMethod('{{ $paymentMethod->id }}')"
                                        class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400"
                                    >
                                        Set as Default
                                    </button>
                                @endif
                                @if($paymentMethods->count() > 1)
                                    <button 
                                        wire:click="deletePaymentMethod('{{ $paymentMethod->id }}')"
                                        wire:confirm="Are you sure you want to delete this payment method?"
                                        class="text-sm text-red-600 hover:text-red-700 dark:text-red-400"
                                    >
                                        Delete
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-neutral-600 dark:text-neutral-400">No payment methods saved.</p>
            @endif
        </div>

        <div class="pt-6 border-t border-neutral-200 dark:border-neutral-700">
            <h3 class="text-lg font-semibold mb-4">Add Payment Method</h3>
            <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-4">
                Payment methods are managed through Stripe. You'll be redirected to add a payment method when subscribing to a paid plan.
            </p>
            <a href="{{ route('billing.plans') }}">
                <x-slate::button color="primary">View Plans</x-slate::button>
            </a>
        </div>
    </x-slate::card>
</div>

