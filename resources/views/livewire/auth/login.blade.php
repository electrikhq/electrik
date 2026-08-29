@php
    $socialiteProviders = class_exists(\Laravel\Socialite\SocialiteServiceProvider::class)
        ? array_values(array_intersect(['google', 'github', 'apple', 'microsoft'], (array) config('electrik.auth.socialite.providers', [])))
        : [];
    $socialiteLabels = [
        'google' => 'Google',
        'github' => 'GitHub',
        'apple' => 'Apple',
        'microsoft' => 'Microsoft',
    ];
    $magicLinkEnabled = \Electrik\Support\MagicLink::enabled();
    $hasPasskeys = class_exists(\Laravel\Passkeys\PasskeysServiceProvider::class);
@endphp
<div>
    <x-slate::card>
        <x-slate::card-header>
            <x-slate::card-title>
                @if ($requiresTwoFactor)
                    {{ __('Two-factor authentication') }}
                @else
                    {{ __('Sign in') }}
                @endif
            </x-slate::card-title>
            <x-slate::card-description>
                @if ($requiresTwoFactor)
                    {{ __('Enter the code from your authenticator app or a recovery code.') }}
                @elseif ($inviteTeamName)
                    {{ __('Sign in to accept your invitation to :team.', ['team' => $inviteTeamName]) }}
                @else
                    {{ __('Welcome back. Enter your credentials to continue.') }}
                @endif
            </x-slate::card-description>
        </x-slate::card-header>

        <x-slate::card-content>
            @if (session('status'))
                <x-slate::alert variant="success" :title="session('status')" class="mb-4" />
            @endif
            @if (session('error'))
                <x-slate::alert variant="destructive" :title="session('error')" class="mb-4" />
            @endif

            <x-slate::form wire:submit="login" class="space-y-4">
                @unless ($requiresTwoFactor)
                    <x-slate::input
                        wire:model="email"
                        type="email"
                        label="{{ __('Email') }}"
                        autocomplete="username"
                        required
                        :readonly="$inviteLockedEmail"
                    />

                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <x-slate::field-label for="login-password">{{ __('Password') }}</x-slate::field-label>
                            <a href="{{ route('password.request') }}" class="text-sm text-muted-foreground underline-offset-4 hover:underline" wire:navigate>
                                {{ __('Forgot password?') }}
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
                    <x-slate::checkbox wire:model="remember" label="{{ __('Remember me') }}" />
                @else
                    <x-slate::input
                        wire:model="twoFactorCode"
                        label="{{ __('Verification code') }}"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        required
                    />
                @endunless

                <x-slate::button type="submit" class="w-full" size="lg" wire:loading.attr="disabled">
                    @if ($requiresTwoFactor)
                        <span wire:loading.remove wire:target="login">{{ __('Verify') }}</span>
                        <span wire:loading wire:target="login">{{ __('Verifying…') }}</span>
                    @else
                        <span wire:loading.remove wire:target="login">{{ __('Sign in') }}</span>
                        <span wire:loading wire:target="login">{{ __('Signing in…') }}</span>
                    @endif
                </x-slate::button>
            </x-slate::form>

            @if (! $requiresTwoFactor && ($hasPasskeys || count($socialiteProviders) > 0 || $magicLinkEnabled))
                <div class="mt-4 space-y-3">
                    <div class="relative py-1 text-center text-xs text-muted-foreground">
                        <span class="bg-card px-2 relative z-10">{{ __('or') }}</span>
                        <span class="absolute inset-x-0 top-1/2 border-t border-border/80"></span>
                    </div>

                    @if ($hasPasskeys)
                        <div
                            x-data="{
                                busy: false,
                                error: null,
                                async signIn() {
                                    this.error = null;
                                    this.busy = true;
                                    try {
                                        const result = await window.ElectrikPasskeys.login({
                                            remember: !!$wire.remember,
                                        });
                                        window.location.href = result.redirect || @js(config('electrik.auth.home', '/dashboard'));
                                    } catch (e) {
                                        this.error = window.ElectrikPasskeys?.friendlyError?.(e, 'Unable to sign in with passkey.')
                                            || 'Unable to sign in with passkey.';
                                    } finally {
                                        this.busy = false;
                                    }
                                }
                            }"
                        >
                            <p x-show="error" x-cloak x-text="error" class="mb-2 text-sm text-destructive"></p>
                            <x-slate::button
                                type="button"
                                variant="outline"
                                class="w-full"
                                x-on:click="signIn()"
                                x-bind:disabled="busy"
                                x-bind:aria-busy="busy"
                            >
                                <span x-text="busy ? @js(__('Waiting for authenticator…')) : @js(__('Sign in with passkey'))">
                                    {{ __('Sign in with passkey') }}
                                </span>
                            </x-slate::button>
                        </div>
                        @include('electrik::partials.passkeys-script')
                    @endif

                    @foreach ($socialiteProviders as $provider)
                        <x-slate::button
                            as="a"
                            href="{{ route('socialite.redirect', $provider) }}"
                            variant="outline"
                            class="w-full"
                        >
                            {{ __('Continue with :provider', ['provider' => $socialiteLabels[$provider] ?? ucfirst($provider)]) }}
                        </x-slate::button>
                    @endforeach

                    @if ($magicLinkEnabled)
                        <div x-data="{ open: false }">
                            <x-slate::button
                                type="button"
                                variant="outline"
                                class="w-full"
                                x-show="! open"
                                x-on:click="open = true"
                            >
                                {{ __('Email me a sign-in link') }}
                            </x-slate::button>
                            <form
                                method="POST"
                                action="{{ route('magic-link.store') }}"
                                class="space-y-2"
                                x-show="open"
                                x-cloak
                            >
                                @csrf
                                <x-slate::input
                                    type="email"
                                    name="email"
                                    label="{{ __('Email') }}"
                                    value="{{ old('email', $email) }}"
                                    required
                                />
                                <x-slate::button type="submit" variant="outline" class="w-full">
                                    {{ __('Send sign-in link') }}
                                </x-slate::button>
                            </form>
                        </div>
                    @endif
                </div>
            @endif
        </x-slate::card-content>

        @if (config('electrik.auth.registration', true) && ! $requiresTwoFactor)
            <x-slate::card-footer class="justify-center">
                <p class="text-sm text-muted-foreground">
                    {{ __('No account?') }}
                    <a href="{{ route('register') }}" class="font-medium text-foreground underline-offset-4 hover:underline" wire:navigate>
                        {{ __('Create one') }}
                    </a>
                </p>
            </x-slate::card-footer>
        @endif
    </x-slate::card>
</div>
