<div class="flex flex-col items-start w-full px-6 py-6 h-full">
    <h2 class="text-xl font-medium items-center flex space-x-4">Billing</h2>
    
    @php
        $currentRouteName = Route::currentRouteName();
    @endphp

    <div class="mt-6 w-full">
        <h3 class="text-sm font-semibold text-neutral-500 dark:text-neutral-400 uppercase mb-3">Subscription</h3>
        <a class="hover:underline block mt-2 {{ $currentRouteName === 'billing.index' ? 'underline text-primary-600 dark:text-primary-700' : '' }}" href="{{ route('billing.index') }}">Overview</a>
        <a class="hover:underline block mt-3 {{ $currentRouteName === 'billing.plans' ? 'underline text-primary-600 dark:text-primary-700' : '' }}" href="{{ route('billing.plans') }}">Plans</a>
        <a class="hover:underline block mt-3 {{ $currentRouteName === 'billing.subscription' ? 'underline text-primary-600 dark:text-primary-700' : '' }}" href="{{ route('billing.subscription') }}">Subscription</a>
    </div>

    <div class="mt-6 w-full">
        <h3 class="text-sm font-semibold text-neutral-500 dark:text-neutral-400 uppercase mb-3">Payment</h3>
        <a class="hover:underline block mt-2 {{ $currentRouteName === 'billing.payment-methods' ? 'underline text-primary-600 dark:text-primary-700' : '' }}" href="{{ route('billing.payment-methods') }}">Payment Methods</a>
        <a class="hover:underline block mt-3 {{ $currentRouteName === 'billing.address' ? 'underline text-primary-600 dark:text-primary-700' : '' }}" href="{{ route('billing.address') }}">Billing Address</a>
        <a class="hover:underline block mt-3 {{ $currentRouteName === 'billing.invoices' ? 'underline text-primary-600 dark:text-primary-700' : '' }}" href="{{ route('billing.invoices') }}">Invoices</a>
    </div>
</div>

