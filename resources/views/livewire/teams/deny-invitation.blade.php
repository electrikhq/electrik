<div>
    <x-slate::card>
        <x-slate::card-header>
            <x-slate::card-title>{{ __('Decline invitation') }}</x-slate::card-title>
            <x-slate::card-description>
                @if ($error)
                    {{ $error }}
                @elseif ($declined)
                    @if ($teamName)
                        {{ __('You declined the invitation to :team.', ['team' => $teamName]) }}
                    @else
                        {{ __('You declined the invitation.') }}
                    @endif
                @else
                    {{ __('Decline the invitation to :team?', ['team' => $teamName]) }}
                @endif
            </x-slate::card-description>
        </x-slate::card-header>

        @unless ($error || $declined)
            <x-slate::card-content>
                <x-electrik::confirm
                    :title="__('Decline this invitation?')"
                    :description="__('You will not join :team.', ['team' => $teamName])"
                    :confirm-label="__('Decline')"
                    wire-click="deny"
                    class="w-full"
                >
                    <x-slate::button type="button" variant="destructive" class="w-full" size="lg">
                        {{ __('Decline invitation') }}
                    </x-slate::button>
                </x-electrik::confirm>
            </x-slate::card-content>
        @endunless

        @if ($declined)
            <x-slate::card-content>
                <x-slate::button as="a" href="{{ route('login') }}" class="w-full" size="lg" wire:navigate>
                    {{ __('Continue') }}
                </x-slate::button>
            </x-slate::card-content>
        @endif
    </x-slate::card>
</div>
