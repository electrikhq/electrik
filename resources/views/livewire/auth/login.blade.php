<div>
    <x-slate::card>
        <x-slate::card-header>
            <x-slate::card-title>
                @if ($requiresTwoFactor)
                    Two-factor authentication
                @else
                    Sign in
                @endif
            </x-slate::card-title>
            <x-slate::card-description>
                @if ($requiresTwoFactor)
                    Enter the code from your authenticator app or a recovery code.
                @elseif ($inviteTeamName)
                    Sign in to accept your invitation to {{ $inviteTeamName }}.
                @else
                    Welcome back. Enter your credentials to continue.
                @endif
            </x-slate::card-description>
        </x-slate::card-header>

        <x-slate::card-content>
            <x-slate::form wire:submit="login" class="space-y-4">
                @unless ($requiresTwoFactor)
                    <x-slate::input
                        wire:model="email"
                        type="email"
                        label="Email"
                        autocomplete="username"
                        required
                        :readonly="$inviteLockedEmail"
                    />

                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <x-slate::field-label for="login-password">Password</x-slate::field-label>
                            <a href="{{ route('password.request') }}" class="text-sm text-muted-foreground underline-offset-4 hover:underline" wire:navigate>
                                Forgot password?
                            </a>
                        </div>
                        <x-slate::input
                            id="login-password"
                            wire:model="password"
                            type="password"
                            autocomplete="current-password"
                            required
                            :showError="true"
                        />
                    </div>
                    <x-slate::checkbox wire:model="remember" label="Remember me" />
                @else
                    <x-slate::input
                        wire:model="twoFactorCode"
                        label="Verification code"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        required
                    />
                @endunless

                <x-slate::button type="submit" class="w-full" size="lg" wire:loading.attr="disabled">
                    @if ($requiresTwoFactor)
                        <span wire:loading.remove wire:target="login">Verify</span>
                        <span wire:loading wire:target="login">Verifying…</span>
                    @else
                        <span wire:loading.remove wire:target="login">Sign in</span>
                        <span wire:loading wire:target="login">Signing in…</span>
                    @endif
                </x-slate::button>
            </x-slate::form>
        </x-slate::card-content>

        @if (config('electrik.auth.registration', true) && ! $requiresTwoFactor)
            <x-slate::card-footer class="justify-center">
                <p class="text-sm text-muted-foreground">
                    No account?
                    <a href="{{ route('register') }}" class="font-medium text-foreground underline-offset-4 hover:underline" wire:navigate>
                        Create one
                    </a>
                </p>
            </x-slate::card-footer>
        @endif
    </x-slate::card>
</div>
