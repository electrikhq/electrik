<div>
    <x-slate::card>
        <x-slate::card-header>
            <x-slate::card-title>Forgot password</x-slate::card-title>
            <x-slate::card-description>
                Enter your email and we will send a reset link.
            </x-slate::card-description>
        </x-slate::card-header>

        <x-slate::card-content class="space-y-4">
            @if ($status)
                <x-slate::alert variant="success" :title="$status" />
            @endif

            <x-slate::form wire:submit="sendResetLink" class="space-y-4">
                <x-slate::input
                    wire:model="email"
                    type="email"
                    label="Email"
                    autocomplete="username"
                    required
                />

                <x-slate::button type="submit" class="w-full" size="lg" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="sendResetLink">Email reset link</span>
                    <span wire:loading wire:target="sendResetLink">Sending…</span>
                </x-slate::button>
            </x-slate::form>
        </x-slate::card-content>

        <x-slate::card-footer class="justify-center">
            <a href="{{ route('login') }}" class="text-sm text-muted-foreground underline-offset-4 hover:underline" wire:navigate>
                Back to sign in
            </a>
        </x-slate::card-footer>
    </x-slate::card>
</div>
