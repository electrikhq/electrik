<div class="mx-auto max-w-lg space-y-6">
    <x-electrik::page-header
        title="{{ __('Security') }}"
        description="{{ __('Password, passkeys, and two-factor authentication.') }}"
    />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-header>
            <x-slate::card-title>{{ __('Change password') }}</x-slate::card-title>
        </x-slate::card-header>
        <x-slate::card-content>
            <x-slate::form wire:submit="updatePassword" class="space-y-4">
                <x-slate::input
                    wire:model="current_password"
                    type="password"
                    label="{{ __('Current password') }}"
                    autocomplete="current-password"
                    required
                />

                <x-slate::input
                    wire:model="password"
                    type="password"
                    label="{{ __('New password') }}"
                    autocomplete="new-password"
                    required
                />

                <x-slate::input
                    wire:model="password_confirmation"
                    type="password"
                    label="{{ __('Confirm new password') }}"
                    autocomplete="new-password"
                    required
                />

                <x-slate::button type="submit" size="lg" wire:loading.attr="disabled">
                    {{ __('Update password') }}
                </x-slate::button>
            </x-slate::form>
        </x-slate::card-content>
    </x-slate::card>

    @if ($passkeysEnabled)
        <x-slate::card
            class="border-border/80 shadow-xs"
            x-data="{
                busy: false,
                error: null,
                async register() {
                    if (this.busy) return;
                    this.error = null;
                    this.busy = true;
                    try {
                        if (! window.ElectrikPasskeys) {
                            throw new Error('Passkey script failed to load. Refresh and try again.');
                        }
                        await window.ElectrikPasskeys.register({
                            name: ($wire.passkeyName || '').trim() || 'Passkey',
                        });
                        await $wire.passkeyRegistered();
                    } catch (e) {
                        this.error = window.ElectrikPasskeys?.friendlyError?.(e, 'Unable to register passkey.')
                            || 'Unable to register passkey.';
                    } finally {
                        this.busy = false;
                    }
                }
            }"
        >
            <x-slate::card-header>
                <x-slate::card-title>{{ __('Passkeys') }}</x-slate::card-title>
                <x-slate::card-description>
                    {{ __('Sign in with Face ID, Touch ID, or a security key.') }}
                </x-slate::card-description>
            </x-slate::card-header>
            <x-slate::card-content class="space-y-4">
                @if ($passkeys->isNotEmpty())
                    <ul class="divide-y divide-border/80 rounded-lg border border-border/80">
                        @foreach ($passkeys as $passkey)
                            <li class="flex items-center justify-between gap-3 px-3 py-2 text-sm" wire:key="passkey-{{ $passkey->getKey() }}">
                                <div>
                                    <p class="font-medium">{{ $passkey->name }}</p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ __('Added :date', ['date' => optional($passkey->created_at)?->toFormattedDateString()]) }}
                                        @if ($passkey->last_used_at)
                                            · {{ __('Last used :date', ['date' => $passkey->last_used_at->diffForHumans()]) }}
                                        @endif
                                    </p>
                                </div>
                                <x-electrik::confirm
                                    :title="__('Remove this passkey?')"
                                    :description="__('It will no longer work for sign-in on this account. You may still need to delete it from your device or password manager.')"
                                    :confirm-label="__('Remove')"
                                    wire-click="deletePasskey('{{ $passkey->getKey() }}')"
                                >
                                    <x-slate::button type="button" variant="outline" size="sm">
                                        {{ __('Remove') }}
                                    </x-slate::button>
                                </x-electrik::confirm>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-muted-foreground">{{ __('No passkeys yet.') }}</p>
                @endif

                <div class="space-y-3">
                    <x-slate::input wire:model="passkeyName" label="{{ __('Device name') }}" placeholder="{{ __('MacBook Pro') }}" />
                    <p x-show="error" x-cloak x-text="error" class="text-sm text-destructive"></p>
                    <x-slate::button
                        type="button"
                        x-on:click="register()"
                        x-bind:disabled="busy"
                        x-bind:aria-busy="busy"
                    >
                        <span x-text="busy ? @js(__('Waiting for authenticator…')) : @js(__('Add passkey'))">
                            {{ __('Add passkey') }}
                        </span>
                    </x-slate::button>
                </div>
            </x-slate::card-content>
        </x-slate::card>
    @endif

    @if ($twoFactorAvailable)
        <x-slate::card class="border-border/80 shadow-xs">
            <x-slate::card-header>
                <x-slate::card-title>{{ __('Two-factor authentication') }}</x-slate::card-title>
                <x-slate::card-description>
                    {{ __('Add an extra layer of security to your account.') }}
                </x-slate::card-description>
            </x-slate::card-header>
            <x-slate::card-content class="space-y-4">
                @if ($twoFactorEnabled)
                    <x-slate::alert variant="success" title="{{ __('Two-factor authentication is enabled.') }}" />

                    @if ($recoveryCodes !== [])
                        <div class="rounded-lg border border-border/80 bg-muted/40 p-4">
                            <p class="mb-2 text-sm font-medium">{{ __('Recovery codes') }}</p>
                            <p class="mb-3 text-xs text-muted-foreground">{{ __('Store these codes somewhere safe. Each can be used once.') }}</p>
                            <ul class="grid grid-cols-2 gap-2 font-mono text-sm">
                                @foreach ($recoveryCodes as $code)
                                    <li>{{ $code }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <x-slate::form wire:submit="disableTwoFactor" class="space-y-4">
                        <x-slate::input
                            wire:model="current_password"
                            type="password"
                            label="{{ __('Current password') }}"
                            autocomplete="current-password"
                            required
                        />
                        <x-slate::button type="submit" variant="destructive">{{ __('Disable two-factor') }}</x-slate::button>
                    </x-slate::form>
                @elseif ($showSetup && $setupSecret)
                    <div class="space-y-4">
                        @if ($qrInline)
                            <div class="mx-auto flex w-[180px] justify-center overflow-hidden rounded-lg border border-border/80" aria-hidden="true">
                                {!! $qrInline !!}
                            </div>
                        @endif

                        <p class="text-center text-xs text-muted-foreground">
                            {{ __('Manual key:') }} <code class="font-mono">{{ $setupSecret }}</code>
                        </p>

                        <x-slate::form wire:submit="confirmTwoFactorSetup" class="space-y-4">
                            <x-slate::input
                                wire:model="confirmationCode"
                                label="{{ __('Verification code') }}"
                                inputmode="numeric"
                                autocomplete="one-time-code"
                                required
                            />
                            <x-slate::button type="submit">{{ __('Confirm and enable') }}</x-slate::button>
                        </x-slate::form>
                    </div>
                @else
                    <x-slate::button type="button" wire:click="beginTwoFactorSetup">
                        {{ __('Enable two-factor authentication') }}
                    </x-slate::button>
                @endif
            </x-slate::card-content>
        </x-slate::card>
    @endif
</div>

@if ($passkeysEnabled)
    @include('electrik::partials.passkeys-script')
@endif
