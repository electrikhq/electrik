<div>
    <x-slate::form wire:submit.prevent="resetPassword">
        @if (session('status'))
            <x-slate::alert color="success">
                {{ session('status') }}
            </x-slate::alert>
        @endif

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
            placeholder="New password"
            required
        />

        <x-slate::input 
            name="password_confirmation" 
            type="password"
            label="Confirm Password"
            wire:model="password_confirmation"
            placeholder="Confirm new password"
            required
        />

        <div>
            <x-slate::button type="submit" color="primary" fullWidth>
                Reset Password
            </x-slate::button>
        </div>
    </x-slate::form>
</div>

