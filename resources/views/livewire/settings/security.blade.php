<div class="mx-auto max-w-lg space-y-6">
    <x-electrik::page-header
        title="Security"
        description="Change your password."
    />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-header>
            <x-slate::card-title>Change password</x-slate::card-title>
        </x-slate::card-header>
        <x-slate::card-content>
            <x-slate::form wire:submit="updatePassword" class="space-y-4">
                <x-slate::input
                    wire:model="current_password"
                    type="password"
                    label="Current password"
                    autocomplete="current-password"
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
                    label="Confirm new password"
                    autocomplete="new-password"
                    required
                />

                <x-slate::button type="submit" size="lg" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="updatePassword">Update password</span>
                    <span wire:loading wire:target="updatePassword">Updating…</span>
                </x-slate::button>
            </x-slate::form>
        </x-slate::card-content>
    </x-slate::card>
</div>
