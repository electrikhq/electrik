<div>
    <x-slate::card>
        <x-slate::card-header>
            <x-slate::card-title>Create account</x-slate::card-title>
            <x-slate::card-description>
                @if ($inviteTeamName)
                    Create an account to join {{ $inviteTeamName }}.
                @else
                    Start with your name, email, and a password.
                @endif
            </x-slate::card-description>
        </x-slate::card-header>

        <x-slate::card-content>
            <x-slate::form wire:submit="register" class="space-y-4">
                <x-slate::input
                    wire:model="name"
                    type="text"
                    label="Name"
                    autocomplete="name"
                    required
                />

                <x-slate::input
                    wire:model="email"
                    type="email"
                    label="Email"
                    autocomplete="username"
                    required
                    :readonly="$inviteLockedEmail"
                />

                <x-slate::input
                    wire:model="password"
                    type="password"
                    label="Password"
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
                    <span wire:loading.remove wire:target="register">Create account</span>
                    <span wire:loading wire:target="register">Creating…</span>
                </x-slate::button>
            </x-slate::form>
        </x-slate::card-content>

        <x-slate::card-footer class="justify-center">
            <p class="text-sm text-muted-foreground">
                Already registered?
                <a href="{{ route('login') }}" class="font-medium text-foreground underline-offset-4 hover:underline" wire:navigate>
                    Sign in
                </a>
            </p>
        </x-slate::card-footer>
    </x-slate::card>
</div>
