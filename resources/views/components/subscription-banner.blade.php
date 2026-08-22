@props([
    'team' => null,
])

@php
    use Electrik\Support\BillingStatus;

    $team ??= auth()->user()?->currentTeam;
    $show = BillingStatus::shouldShowBanner($team);
@endphp

@if ($show)
    <div
        data-slot="subscription-banner"
        class="border-b border-primary/20 bg-primary px-4 py-2.5 text-primary-foreground"
        role="status"
    >
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3">
            <p class="text-sm">
                {{ __('You are not subscribed to a plan. Choose a plan to unlock the app.') }}
            </p>
            <x-slate::button
                as="a"
                href="{{ route('billing.plans') }}"
                size="sm"
                variant="secondary"
                wire:navigate
            >
                {{ __('View plans') }}
            </x-slate::button>
        </div>
    </div>
@endif
