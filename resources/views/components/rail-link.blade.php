@props([
    'href' => '#',
    'active' => false,
    'label' => null,
])

@php
    $classes = $active
        ? 'bg-sidebar-accent text-sidebar-accent-foreground'
        : 'text-sidebar-foreground/70 hover:bg-sidebar-accent/70 hover:text-sidebar-accent-foreground';
@endphp

<a
    href="{{ $href }}"
    wire:navigate
    {{ $attributes->merge([
        'class' => "inline-flex size-10 items-center justify-center rounded-lg transition-colors {$classes}",
        'aria-current' => $active ? 'page' : null,
        'aria-label' => $label,
        'title' => $label,
    ]) }}
>
    {{ $slot }}
</a>
