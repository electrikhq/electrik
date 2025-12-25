<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Profile Settings</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-1">Manage your account information</p>
    </div>

    @if(session('message'))
        <x-slate::alert type="success" class="mb-6">
            {{ session('message') }}
        </x-slate::alert>
    @endif

    <x-slate::card>
        <x-slate::form wire:submit="update">
            <x-slate::input 
                name="name" 
                label="Name"
                wire:model="name"
                placeholder="Enter your name"
                required
            />

            <x-slate::input 
                name="email" 
                label="Email"
                type="email"
                wire:model="email"
                placeholder="Enter your email"
                required
            />

            <x-slate::select 
                name="timezone" 
                label="Timezone"
                wire:model="timezone"
                required
            >
                @foreach(timezones() as $timezone => $label)
                    <option value="{{ $timezone }}" {{ $timezone === $this->timezone ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </x-slate::select>

            <div class="flex gap-3 mt-6">
                <x-slate::button type="submit" color="primary">Update Profile</x-slate::button>
            </div>
        </x-slate::form>
    </x-slate::card>
</div>

