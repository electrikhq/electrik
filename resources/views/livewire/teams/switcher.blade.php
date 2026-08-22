<div class="space-y-1">
    <p class="px-2.5 text-[11px] font-medium uppercase tracking-wider text-muted-foreground">Switch team</p>
    @forelse ($teams as $team)
        @if ((int) $currentTeamId === (int) $team->id)
            <span class="flex h-9 items-center rounded-md bg-sidebar-accent px-2.5 text-sm font-medium text-sidebar-accent-foreground">
                {{ $team->name }}
            </span>
        @else
            <button
                type="button"
                wire:click="switch({{ $team->id }})"
                class="flex h-9 w-full items-center rounded-md px-2.5 text-left text-sm font-medium text-sidebar-foreground/80 transition-colors hover:bg-sidebar-accent/70 hover:text-sidebar-accent-foreground"
            >
                {{ $team->name }}
            </button>
        @endif
    @empty
        <p class="px-2.5 text-xs text-muted-foreground">No teams</p>
    @endforelse
</div>
