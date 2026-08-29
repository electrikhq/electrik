@props([
    'title' => 'Are you sure?',
    'description' => null,
    'confirmLabel' => 'Continue',
    'cancelLabel' => 'Cancel',
    'confirmVariant' => 'destructive',
    'wireClick' => '',
])

<x-slate::alert-dialog data-slot="electrik-confirm" {{ $attributes }}>
    <x-slate::alert-dialog-trigger>
        {{ $trigger ?? $slot }}
    </x-slate::alert-dialog-trigger>

    <x-slate::alert-dialog-content
        :title="$title"
        :description="$description"
    >
        <x-slot:footer>
            <x-slate::alert-dialog-cancel>
                <x-slate::button type="button" variant="outline">
                    {{ $cancelLabel }}
                </x-slate::button>
            </x-slate::alert-dialog-cancel>
            <x-slate::alert-dialog-action>
                <x-slate::button
                    type="button"
                    variant="{{ $confirmVariant }}"
                    wire:click="{{ $wireClick }}"
                >
                    {{ $confirmLabel }}
                </x-slate::button>
            </x-slate::alert-dialog-action>
        </x-slot:footer>
    </x-slate::alert-dialog-content>
</x-slate::alert-dialog>
