<div>
    <x-slate::form wire:submit="register">
        <x-slate::input 
            name="name" 
            label="Name"
            wire:model="name"
            placeholder="Your name"
            required
        />

        <x-slate::input 
            name="email" 
            type="email"
            label="Email address"
            wire:model="email"
            placeholder="Email address"
            required
        />

        <x-slate::input 
            name="password" 
            type="password"
            label="Password"
            wire:model="password"
            placeholder="Password"
            required
        />

        <x-slate::input 
            name="password_confirmation" 
            type="password"
            label="Confirm Password"
            wire:model="password_confirmation"
            placeholder="Confirm password"
            required
        />

        <x-slate::select 
            name="timezone" 
            label="Timezone"
            wire:model="timezone"
            :options="timezones()"
            required
        />

        <div>
            <x-slate::button type="submit" color="primary" fullWidth>
                Create Account
            </x-slate::button>
        </div>

        <div class="text-center">
            <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                Already have an account? Sign in
            </a>
        </div>
    </x-slate::form>
</div>

