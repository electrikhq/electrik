<div>
    <x-slate::form wire:submit="sendResetLink">
        @if ($status)
            <x-slate::alert color="success">
                {{ $status }}
            </x-slate::alert>
        @endif

        <div class="text-sm text-gray-600 mb-4">
            Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
        </div>

        <x-slate::input 
            name="email" 
            type="email"
            label="Email address"
            wire:model="email"
            placeholder="Email address"
            required
        />

        <div>
            <x-slate::button type="submit" color="primary" fullWidth>
                Email Password Reset Link
            </x-slate::button>
        </div>

        <div class="text-center">
            <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                Back to login
            </a>
        </div>
    </x-slate::form>
</div>

