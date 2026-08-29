@props([
    'class' => '',
    'size' => 'sm', // sm | md
])

@php
    $name = config('electrik.name', 'Electrik');
    $logo = config('electrik.branding.logo_url');
    $logoDark = config('electrik.branding.logo_dark_url') ?: $logo;
    $sizeClass = $size === 'md' ? 'h-8 max-w-[10rem]' : 'h-6 max-w-[8rem]';
@endphp

@if (filled($logo))
    <span {{ $attributes->class(['inline-flex items-center', $class]) }}>
        <img
            src="{{ $logo }}"
            alt="{{ $name }}"
            class="{{ $sizeClass }} w-auto object-contain dark:hidden"
        />
        <img
            src="{{ $logoDark }}"
            alt="{{ $name }}"
            class="{{ $sizeClass }} w-auto object-contain hidden dark:block"
        />
    </span>
@else
    <span {{ $attributes->class(['font-semibold tracking-tight text-foreground', $class]) }}>
        {{ $name }}
    </span>
@endif
