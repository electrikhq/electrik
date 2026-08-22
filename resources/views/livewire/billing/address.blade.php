<div class="space-y-6">

    <x-electrik::page-header
        title="Billing address"
        description="Synced to the Stripe customer for this team."
    />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif
    @if (session('error'))
        <x-slate::alert variant="destructive" :title="session('error')" />
    @endif

    <form wire:submit="save" class="max-w-xl space-y-4">
        <x-slate::input wire:model="name" label="Name" required />
        @error('name') <p class="text-sm text-destructive">{{ $message }}</p> @enderror

        <x-slate::input wire:model="email" type="email" label="Email" required />
        @error('email') <p class="text-sm text-destructive">{{ $message }}</p> @enderror

        <x-slate::input wire:model="line1" label="Address line 1" required />
        @error('line1') <p class="text-sm text-destructive">{{ $message }}</p> @enderror

        <x-slate::input wire:model="line2" label="Address line 2" />

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-slate::input wire:model="city" label="City" required />
                @error('city') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
            </div>
            <div>
                <x-slate::input wire:model="state" label="State / region" required />
                @error('state') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-slate::input wire:model="postal_code" label="Postal code" required />
                @error('postal_code') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
            </div>
            <div>
                <x-slate::input wire:model="country" label="Country (ISO)" maxlength="2" placeholder="US" required />
                @error('country') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
            </div>
        </div>

        <x-slate::button type="submit">Save address</x-slate::button>
    </form>
</div>
