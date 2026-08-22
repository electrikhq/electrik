@props([
    'title' => 'Are you sure?',
    'description' => null,
    'confirmLabel' => 'Continue',
    'cancelLabel' => 'Cancel',
    'confirmVariant' => 'destructive',
    'wireClick' => '',
])

<div
    data-slot="electrik-confirm"
    x-data="{ open: false }"
    x-on:keydown.escape.window="if (open) open = false"
    {{ $attributes }}
>
    <span class="inline-flex" role="button" tabindex="0" x-on:click="open = true" x-on:keydown.enter.prevent="open = true">
        {{ $trigger ?? $slot }}
    </span>

    <template x-teleport="body">
        <div x-show="open" x-cloak class="relative z-50" style="display: none;">
            <div
                class="fixed inset-0 z-50 bg-black/50"
                x-on:click="open = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            ></div>

            <div
                role="alertdialog"
                aria-modal="true"
                class="fixed top-1/2 left-1/2 z-[51] grid w-full max-w-[calc(100%-2rem)] -translate-x-1/2 -translate-y-1/2 gap-4 rounded-lg border bg-background p-6 shadow-lg outline-none sm:max-w-lg"
                x-on:click.stop
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
            >
                <div class="flex flex-col gap-2 text-center sm:text-start">
                    <h2 class="text-lg font-semibold tracking-tight">{{ $title }}</h2>
                    @if (filled($description))
                        <p class="text-sm text-muted-foreground">{{ $description }}</p>
                    @endif
                </div>

                <div class="flex w-full flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <x-slate::button type="button" variant="outline" x-on:click="open = false">
                        {{ $cancelLabel }}
                    </x-slate::button>

                    <x-slate::button
                        type="button"
                        variant="{{ $confirmVariant }}"
                        x-on:click="open = false"
                        wire:click="{{ $wireClick }}"
                    >
                        {{ $confirmLabel }}
                    </x-slate::button>
                </div>
            </div>
        </div>
    </template>
</div>
