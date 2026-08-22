<div class="mx-auto max-w-lg space-y-6">
    <x-electrik::page-header
        title="Profile"
        description="Update your name, email, and timezone."
    />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-content>
            <x-slate::form wire:submit="save" class="space-y-4">
                <x-slate::input wire:model="name" label="Name" autocomplete="name" required />
                <x-slate::input wire:model="email" type="email" label="Email" autocomplete="email" required />

                <x-slate::select wire:model="timezone" label="Timezone" required>
                    @foreach ($timezones as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-slate::select>

                <x-slate::button type="submit" size="lg" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Save</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </x-slate::button>
            </x-slate::form>
        </x-slate::card-content>
    </x-slate::card>
</div>
