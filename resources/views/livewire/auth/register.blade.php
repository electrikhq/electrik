<div>
    <x-slate::card>
        <x-slate::card-header>
            <x-slate::card-title>{{ __('Create account') }}</x-slate::card-title>
            <x-slate::card-description>
                @if ($inviteTeamName)
                    {{ __('Create an account to join :team.', ['team' => $inviteTeamName]) }}
                @else
                    {{ __('Start with your name, email, and a password.') }}
                @endif
            </x-slate::card-description>
        </x-slate::card-header>

        <x-slate::card-content>
            <x-slate::form wire:submit="register" class="space-y-4">
                <x-slate::input
                    wire:model="name"
                    type="text"
                    label="{{ __('Name') }}"
                    autocomplete="name"
                    required
                />

                <x-slate::input
                    wire:model="email"
                    type="email"
                    label="{{ __('Email') }}"
                    autocomplete="username"
                    required
                    :readonly="$inviteLockedEmail"
                />

                <x-slate::input
                    wire:model="password"
                    type="password"
                    label="{{ __('Password') }}"
                    autocomplete="new-password"
                    required
                />

                <x-slate::input
                    wire:model="password_confirmation"
                    type="password"
                    label="{{ __('Confirm password') }}"
                    autocomplete="new-password"
                    required
                />

                <x-slate::button type="submit" class="w-full" size="lg" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="register">{{ __('Create account') }}</span>
                    <span wire:loading wire:target="register">{{ __('Creating…') }}</span>
                </x-slate::button>
            </x-slate::form>
        </x-slate::card-content>

        <x-slate::card-footer class="justify-center">
            <p class="text-sm text-muted-foreground">
                {{ __('Already registered?') }}
                <a href="{{ route('login') }}" class="font-medium text-foreground underline-offset-4 hover:underline" wire:navigate>
                    {{ __('Sign in') }}
                </a>
            </p>
        </x-slate::card-footer>
    </x-slate::card>
</div>
