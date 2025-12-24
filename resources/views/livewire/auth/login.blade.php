<div>
    <x-slate::form wire:submit.prevent="login">
        @if ($error)
            <x-slate::alert color="danger" dismissible>
                {{ $error }}
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
            placeholder="Password"
            required
        />

        <div class="flex items-center justify-between">
            <x-slate::checkbox 
                name="remember" 
                wire:model="remember"
                label="Remember me"
            />

            <div class="text-sm">
                <a href="{{ route('password.request') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Forgot your password?
                </a>
            </div>
        </div>

        <div>
            <x-slate::button type="submit" color="primary" fullWidth>
                Sign in
            </x-slate::button>
        </div>

        <div class="text-center">
            <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                Don't have an account? Register
            </a>
        </div>
    </x-slate::form>
</div>
