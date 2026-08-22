<div>
    <x-slate::card>
        <x-slate::card-header>
            <x-slate::card-title>Decline invitation</x-slate::card-title>
            <x-slate::card-description>
                @if ($error)
                    {{ $error }}
                @elseif ($declined)
                    You declined the invitation{{ $teamName ? ' to '.$teamName : '' }}.
                @else
                    Decline the invitation to {{ $teamName }}?
                @endif
            </x-slate::card-description>
        </x-slate::card-header>

        @unless ($error || $declined)
            <x-slate::card-content>
                <x-electrik::confirm
                    title="Decline this invitation?"
                    description="You will not join {{ $teamName }}."
                    confirm-label="Decline"
                    wire-click="deny"
                    class="w-full"
                >
                    <x-slate::button type="button" variant="destructive" class="w-full" size="lg">
                        Decline invitation
                    </x-slate::button>
                </x-electrik::confirm>
            </x-slate::card-content>
        @endunless

        @if ($declined)
            <x-slate::card-content>
                <x-slate::button as="a" href="{{ route('login') }}" class="w-full" size="lg" wire:navigate>
                    Continue
                </x-slate::button>
            </x-slate::card-content>
        @endif
    </x-slate::card>
</div>
