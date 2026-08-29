<div class="space-y-6">

    <x-electrik::page-header
        title="{{ __('Billing address') }}"
        description="{{ __('Synced to the Stripe customer for this team.') }}"
    />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif
    @if (session('error'))
        <x-slate::alert variant="destructive" :title="session('error')" />
    @endif

    <x-slate::form wire:submit="save" class="max-w-xl space-y-4">
        <x-slate::input wire:model="name" label="{{ __('Name') }}" required />
        <x-slate::input wire:model="email" type="email" label="{{ __('Email') }}" required />
        <x-slate::input wire:model="line1" label="{{ __('Address line 1') }}" required />
        <x-slate::input wire:model="line2" label="{{ __('Address line 2') }}" />

        <div class="grid gap-4 sm:grid-cols-2">
            <x-slate::input wire:model="city" label="{{ __('City') }}" required />
            <x-slate::input wire:model="state" label="{{ __('State / region') }}" required />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <x-slate::input wire:model="postal_code" label="{{ __('Postal code') }}" required />
            <x-slate::select wire:model="country" label="{{ __('Country') }}" required>
                @foreach ($countries as $code => $label)
                    <option value="{{ $code }}">{{ $label }}</option>
                @endforeach
            </x-slate::select>
        </div>

        <x-slate::button type="submit">{{ __('Save address') }}</x-slate::button>
    </x-slate::form>

    <x-slate::card class="max-w-xl border-border/80 shadow-xs">
        <x-slate::card-header>
            <x-slate::card-title>{{ __('Tax IDs') }}</x-slate::card-title>
            <x-slate::card-description>
                {{ __('VAT / GST / EIN identifiers stored on the Stripe customer.') }}
            </x-slate::card-description>
        </x-slate::card-header>
        <x-slate::card-content class="space-y-4">
            @forelse ($taxIds as $taxId)
                <div class="flex items-center justify-between gap-3 rounded-xl border border-border/80 px-4 py-3" wire:key="tax-{{ $taxId['id'] }}">
                    <div>
                        <p class="font-medium">{{ $taxIdTypes[$taxId['type']] ?? strtoupper($taxId['type']) }}</p>
                        <p class="text-xs text-muted-foreground">{{ $taxId['value'] }}</p>
                    </div>
                    <x-slate::button type="button" variant="outline" size="sm" wire:click="removeTaxId('{{ $taxId['id'] }}')">
                        {{ __('Remove') }}
                    </x-slate::button>
                </div>
            @empty
                <p class="text-sm text-muted-foreground">{{ __('No tax IDs yet.') }}</p>
            @endforelse

            <x-slate::form wire:submit="addTaxId" class="space-y-3">
                <x-slate::select wire:model="taxIdType" label="{{ __('Type') }}" required>
                    <option value="">{{ __('Select…') }}</option>
                    @foreach ($taxIdTypes as $code => $label)
                        <option value="{{ $code }}">{{ $label }}</option>
                    @endforeach
                </x-slate::select>
                <x-slate::input wire:model="taxIdValue" label="{{ __('Value') }}" required />
                <x-slate::button type="submit" size="sm">{{ __('Add tax ID') }}</x-slate::button>
            </x-slate::form>
        </x-slate::card-content>
    </x-slate::card>
</div>
