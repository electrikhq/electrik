@props([
    'team' => null,
])

@php
    use Electrik\Support\BillingStatus;

    $team ??= auth()->user()?->currentTeam;
    $show = BillingStatus::shouldShowBanner($team);
    $cta = BillingStatus::bannerCta($team);
@endphp

@if ($show)
    <div
        data-slot="subscription-banner"
        @class([
            'border-b px-4 py-2.5',
            'border-destructive/20 bg-destructive text-destructive-foreground' => BillingStatus::isPastDue($team),
            'border-primary/20 bg-primary text-primary-foreground' => ! BillingStatus::isPastDue($team),
        ])
        role="status"
    >
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3">
            <p class="text-sm">
                {{ BillingStatus::bannerMessage($team) }}
            </p>
            <x-slate::button
                as="a"
                href="{{ route($cta['route']) }}"
                size="sm"
                variant="secondary"
                wire:navigate
            >
                {{ $cta['label'] }}
            </x-slate::button>
        </div>
    </div>
@endif
