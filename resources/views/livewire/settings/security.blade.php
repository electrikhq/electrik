<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Security Settings</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-1">Manage your password and security preferences</p>
    </div>

    @if(session('message'))
        <x-slate::alert type="success" class="mb-6">
            {{ session('message') }}
        </x-slate::alert>
    @endif

    <x-slate::card>
        <h3 class="text-lg font-semibold mb-4">Change Password</h3>
        
        <x-slate::form wire:submit="updatePassword">
            <x-slate::input 
                name="current_password" 
                label="Current Password"
                type="password"
                wire:model="current_password"
                placeholder="Enter your current password"
                required
            />
            @error('current_password')
                <x-slate::alert type="danger" class="mt-2">
                    {{ $message }}
                </x-slate::alert>
            @enderror

            <x-slate::input 
                name="password" 
                label="New Password"
                type="password"
                wire:model="password"
                placeholder="Enter your new password"
                required
            />

            <x-slate::input 
                name="password_confirmation" 
                label="Confirm New Password"
                type="password"
                wire:model="password_confirmation"
                placeholder="Confirm your new password"
                required
            />

            <div class="flex gap-3 mt-6">
                <x-slate::button type="submit" color="primary">Update Password</x-slate::button>
            </div>
        </x-slate::form>
    </x-slate::card>
</div>

