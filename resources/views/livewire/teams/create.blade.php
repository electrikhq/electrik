<div class="mx-auto max-w-lg space-y-6">
    <x-electrik::page-header
        title="{{ __('Create team') }}"
        description="{{ __('Name your workspace. You will be the owner.') }}"
    />

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-content>
            <x-slate::form wire:submit="create" class="space-y-4">
                <x-slate::input wire:model="name" label="{{ __('Team name') }}" required />
                <x-slate::button type="submit" class="w-full" size="lg">{{ __('Create team') }}</x-slate::button>
            </x-slate::form>
        </x-slate::card-content>
    </x-slate::card>
</div>
