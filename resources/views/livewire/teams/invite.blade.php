<div class="mx-auto max-w-lg space-y-6">
    <x-electrik::page-header
        title="{{ __('Invite member') }}"
        :description="$team->name"
    />

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-content>
            <x-slate::form wire:submit="invite" class="space-y-4">
                <x-slate::input wire:model="email" type="email" label="{{ __('Email') }}" required />

                <x-slate::select wire:model="role" label="{{ __('Role') }}" required>
                    @foreach ($assignableRoles as $roleName)
                        <option value="{{ $roleName }}">{{ ucfirst($roleName) }}</option>
                    @endforeach
                </x-slate::select>

                <x-slate::button type="submit" class="w-full" size="lg">{{ __('Send invitation') }}</x-slate::button>
            </x-slate::form>
        </x-slate::card-content>
    </x-slate::card>
</div>
