<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Billing Address</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-1">Update your billing address</p>
    </div>

    @if(session('message'))
        <x-slate::alert type="success" class="mb-6">
            {{ session('message') }}
        </x-slate::alert>
    @endif

    @if(session('error'))
        <x-slate::alert type="error" class="mb-6">
            {{ session('error') }}
        </x-slate::alert>
    @endif

    <x-slate::card>
        <x-slate::form wire:submit="update">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-slate::input 
                    name="name" 
                    label="Name"
                    wire:model="name"
                    placeholder="Full name"
                    required
                />

                <x-slate::input 
                    name="email" 
                    type="email"
                    label="Email"
                    wire:model="email"
                    placeholder="Email address"
                    required
                />
            </div>

            <x-slate::input 
                name="address_1" 
                label="Address Line 1"
                wire:model="address_1"
                placeholder="Street address"
                required
            />

            <x-slate::input 
                name="address_2" 
                label="Address Line 2"
                wire:model="address_2"
                placeholder="Apartment, suite, etc. (optional)"
            />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-slate::input 
                    name="city" 
                    label="City"
                    wire:model="city"
                    placeholder="City"
                    required
                />

                <x-slate::input 
                    name="state" 
                    label="State/Province"
                    wire:model="state"
                    placeholder="State or Province"
                    required
                />

                <x-slate::input 
                    name="pincode" 
                    label="Postal Code"
                    wire:model="pincode"
                    placeholder="Postal code"
                    required
                />
            </div>

            <x-slate::input 
                name="country" 
                label="Country"
                wire:model="country"
                placeholder="Country"
                required
            />

            <div class="flex gap-3 mt-6">
                <x-slate::button type="submit" color="primary">Update Address</x-slate::button>
                <a href="{{ route('billing.index') }}">
                    <x-slate::button variant="outline">Cancel</x-slate::button>
                </a>
            </div>
        </x-slate::form>
    </x-slate::card>
</div>

