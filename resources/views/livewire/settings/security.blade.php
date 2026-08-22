<div class="mx-auto max-w-lg space-y-6">
    <x-electrik::page-header
        title="Security"
        description="Password and two-factor authentication."
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
                    Update password
                </x-slate::button>
            </x-slate::form>
        </x-slate::card-content>
    </x-slate::card>

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-header>
            <x-slate::card-title>Two-factor authentication</x-slate::card-title>
            <x-slate::card-description>
                Add an extra layer of security to your account.
            </x-slate::card-description>
        </x-slate::card-header>
        <x-slate::card-content class="space-y-4">
            @if ($twoFactorEnabled)
                <x-slate::alert variant="success" title="Two-factor authentication is enabled." />

                @if ($recoveryCodes !== [])
                    <div class="rounded-lg border border-border/80 bg-muted/40 p-4">
                        <p class="mb-2 text-sm font-medium">Recovery codes</p>
                        <p class="mb-3 text-xs text-muted-foreground">Store these codes somewhere safe. Each can be used once.</p>
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
                        label="Current password"
                        autocomplete="current-password"
                        required
                    />
                    <x-slate::button type="submit" variant="destructive">Disable two-factor</x-slate::button>
                </x-slate::form>
            @elseif ($showSetup && $setupSecret)
                <div class="space-y-4">
                    @if ($qrUrl)
                        <img
                            src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($qrUrl) }}"
                            alt="QR code"
                            class="mx-auto rounded-lg border border-border/80"
                            width="180"
                            height="180"
                        />
                    @endif

                    <p class="text-center text-xs text-muted-foreground">
                        Manual key: <code class="font-mono">{{ $setupSecret }}</code>
                    </p>

                    <x-slate::form wire:submit="confirmTwoFactorSetup" class="space-y-4">
                        <x-slate::input
                            wire:model="confirmationCode"
                            label="Verification code"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            required
                        />
                        <x-slate::button type="submit">Confirm and enable</x-slate::button>
                    </x-slate::form>
                </div>
            @else
                <x-slate::button type="button" wire:click="beginTwoFactorSetup">
                    Enable two-factor authentication
                </x-slate::button>
            @endif
        </x-slate::card-content>
    </x-slate::card>
</div>
