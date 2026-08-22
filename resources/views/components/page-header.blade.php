@props([
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-start justify-between gap-4']) }}>
    <div class="min-w-0 space-y-1">
        <h1 class="text-2xl font-semibold tracking-tight text-foreground">{{ $title }}</h1>
        @if (filled($description))
            <p class="max-w-2xl text-sm text-muted-foreground">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex shrink-0 flex-wrap items-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</div>
