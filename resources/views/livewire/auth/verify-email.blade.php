<div>
    <x-slate::card>
        <x-slate::card-header>
            <x-slate::card-title>{{ __('Verify your email') }}</x-slate::card-title>
            <x-slate::card-description>
                {{ __('We sent a verification link to your inbox. Open it to continue.') }}
            </x-slate::card-description>
        </x-slate::card-header>

        <x-slate::card-content class="space-y-4">
            @if ($status)
                <x-slate::alert variant="success" :title="$status" />
            @endif

            <p class="text-sm text-muted-foreground">
                {{ __('Didn’t get the email? Check spam, or request another link.') }}
            </p>

            <x-slate::button type="button" class="w-full" size="lg" wire:click="resend" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="resend">{{ __('Resend verification email') }}</span>
                <span wire:loading wire:target="resend">{{ __('Sending…') }}</span>
            </x-slate::button>
        </x-slate::card-content>

        <x-slate::card-footer class="justify-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-muted-foreground underline-offset-4 hover:underline">
                    {{ __('Sign out') }}
                </button>
            </form>
        </x-slate::card-footer>
    </x-slate::card>
</div>
