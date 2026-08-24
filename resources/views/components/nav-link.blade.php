@props([
    'href' => '#',
    'active' => null,
    'exact' => false,
    'icon' => null,
])

@php
    $isActive = $active;

    if ($isActive === null) {
        $path = parse_url($href, PHP_URL_PATH) ?: $href;
        $isActive = $exact
            ? request()->is(ltrim($path, '/'))
            : request()->is(ltrim($path, '/')) || request()->is(trim($path, '/').'/*');
    }

    $classes = $isActive
        ? 'bg-sidebar-accent text-sidebar-accent-foreground'
        : 'text-sidebar-foreground/80 hover:bg-sidebar-accent/70 hover:text-sidebar-accent-foreground';
@endphp

<a
    href="{{ $href }}"
    wire:navigate
    {{ $attributes->merge([
        'class' => "flex h-9 items-center gap-2.5 rounded-md px-2.5 text-sm font-medium transition-colors {$classes}",
        'aria-current' => $isActive ? 'page' : null,
    ]) }}
>
    @if ($icon)
        @svg('carbon-'.$icon, 'size-4 shrink-0 opacity-70')
    @endif
    <span class="truncate">{{ $slot }}</span>
</a>
