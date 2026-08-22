<div>
    <x-slate::card>
        <x-slate::card-header>
            <x-slate::card-title>Accept invitation</x-slate::card-title>
            <x-slate::card-description>
                @if ($error)
                    {{ $error }}
                @else
                    Join {{ $teamName }}@if ($inviteRole) as {{ $inviteRole }}@endif.
                @endif
            </x-slate::card-description>
        </x-slate::card-header>

        @unless ($error)
            <x-slate::card-content class="space-y-4">
                @if ($inviteEmail)
                    <p class="text-sm text-muted-foreground">
                        Invitation for <span class="font-medium text-foreground">{{ $inviteEmail }}</span>
                    </p>
                @endif

                @error('email')
                    <x-slate::alert variant="destructive" :title="$message" />
                @enderror

                @if ($guest)
                    <div class="flex flex-col gap-2">
                        <x-slate::button as="a" href="{{ route('login') }}" class="w-full" size="lg" wire:navigate>
                            Sign in to accept
                        </x-slate::button>
                        @if (config('electrik.auth.registration', true))
                            <x-slate::button as="a" href="{{ route('register') }}" variant="outline" class="w-full" size="lg" wire:navigate>
                                Create account
                            </x-slate::button>
                        @endif
                    </div>
                @elseif ($errors->has('email'))
                    <p class="text-sm text-muted-foreground">
                        Sign out and use the invited email, or ask for a new invitation.
                    </p>
                @else
                    <x-slate::button type="button" class="w-full" size="lg" wire:click="accept">
                        Accept and continue
                    </x-slate::button>
                @endif
            </x-slate::card-content>
        @endunless
    </x-slate::card>
</div>
