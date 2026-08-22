<div>
    <x-slate::card>
        <x-slate::card-header>
            <x-slate::card-title>Reset password</x-slate::card-title>
            <x-slate::card-description>
                Choose a new password for your account.
            </x-slate::card-description>
        </x-slate::card-header>

        <x-slate::card-content>
            <x-slate::form wire:submit="resetPassword" class="space-y-4">
                <x-slate::input
                    wire:model="email"
                    type="email"
                    label="Email"
                    autocomplete="username"
                    required
                />

                <x-slate::input
                    wire:model="password"
                    type="password"
                    label="New password"
                    autocomplete="new-password"
                    required
                />

                <x-slate::input
                    wire:model="password_confirmation"
                    type="password"
                    label="Confirm password"
                    autocomplete="new-password"
                    required
                />

                <x-slate::button type="submit" class="w-full" size="lg" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="resetPassword">Reset password</span>
                    <span wire:loading wire:target="resetPassword">Saving…</span>
                </x-slate::button>
            </x-slate::form>
        </x-slate::card-content>
    </x-slate::card>
</div>
