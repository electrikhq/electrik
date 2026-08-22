<div>
    <x-slate::card>
        <x-slate::card-header>
            <x-slate::card-title>Verify your email</x-slate::card-title>
            <x-slate::card-description>
                We sent a verification link to your inbox. Open it to continue.
            </x-slate::card-description>
        </x-slate::card-header>

        <x-slate::card-content class="space-y-4">
            @if ($status)
                <x-slate::alert variant="success" :title="$status" />
            @endif

            <p class="text-sm text-muted-foreground">
                Didn’t get the email? Check spam, or request another link.
            </p>

            <x-slate::button type="button" class="w-full" size="lg" wire:click="resend" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="resend">Resend verification email</span>
                <span wire:loading wire:target="resend">Sending…</span>
            </x-slate::button>
        </x-slate::card-content>

        <x-slate::card-footer class="justify-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-muted-foreground underline-offset-4 hover:underline">
                    Sign out
                </button>
            </form>
        </x-slate::card-footer>
    </x-slate::card>
</div>
