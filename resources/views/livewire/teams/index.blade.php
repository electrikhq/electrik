<div class="space-y-6">
    <x-electrik::page-header title="Teams" description="Switch workspace or create a new team.">
        <x-slot:actions>
            <x-slate::button as="a" href="{{ route('teams.create') }}" wire:navigate>New team</x-slate::button>
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
                        <x-slate::badge>Current</x-slate::badge>
                    @else
                        <x-slate::button type="button" variant="outline" size="sm" wire:click="switch({{ $team->id }})">
                            Switch
                        </x-slate::button>
                    @endif
                    <x-slate::button as="a" href="{{ route('teams.members', $team) }}" variant="ghost" size="sm" wire:navigate>
                        Members
                    </x-slate::button>
                    <x-slate::button as="a" href="{{ route('teams.settings', $team) }}" variant="ghost" size="sm" wire:navigate>
                        Settings
                    </x-slate::button>
                </div>
            </div>
        @empty
            <x-slate::alert variant="info" title="No teams yet" description="Create your first team to continue." />
        @endforelse
    </div>
</div>
