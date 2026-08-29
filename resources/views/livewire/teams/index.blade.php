<div class="space-y-6">
    <x-electrik::page-header title="{{ __('Teams') }}" description="{{ __('Switch workspace or create a new team.') }}">
        <x-slot:actions>
            <x-slate::button as="a" href="{{ route('teams.create') }}" wire:navigate>{{ __('New team') }}</x-slate::button>
        </x-slot:actions>
    </x-electrik::page-header>

    <div class="space-y-2">
        @forelse ($teams as $team)
            <div
                class="flex items-center justify-between gap-3 rounded-xl border border-border/80 bg-card px-4 py-3.5 transition-colors hover:bg-accent/30"
                wire:key="team-{{ $team->id }}"
            >
                <div>
                    <p class="font-medium tracking-tight">{{ $team->name }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @if ((int) $currentTeamId === (int) $team->id)
                        <x-slate::badge>{{ __('Current') }}</x-slate::badge>
                    @else
                        <x-slate::button type="button" variant="outline" size="sm" wire:click="switch({{ $team->id }})">
                            {{ __('Switch') }}
                        </x-slate::button>
                    @endif
                    <x-slate::button as="a" href="{{ route('teams.members', $team) }}" variant="ghost" size="sm" wire:navigate>
                        {{ __('Members') }}
                    </x-slate::button>
                    <x-slate::button as="a" href="{{ route('teams.settings', $team) }}" variant="ghost" size="sm" wire:navigate>
                        {{ __('Settings') }}
                    </x-slate::button>
                </div>
            </div>
        @empty
            <x-slate::alert variant="info" title="{{ __('No teams yet') }}" description="{{ __('Create your first team to continue.') }}" />
        @endforelse
    </div>
</div>
