<div class="space-y-6">
    <x-electrik::page-header
        title="Permissions"
        :description="'Catalog for '.$team->name.'. Managed via config + electrik:permissions:sync.'"
    >
        <x-slot:actions>
            <x-slate::button as="a" href="{{ route('teams.roles.index', $team) }}" variant="outline" wire:navigate>Back to roles</x-slate::button>
        </x-slot:actions>
    </x-electrik::page-header>

    @forelse ($permissions as $category => $items)
        <div class="space-y-2">
            <h2 class="text-sm font-medium text-muted-foreground">{{ $category ?: 'General' }}</h2>
            <div class="space-y-2">
                @foreach ($items as $permission)
                    <div class="rounded-xl border border-border/80 bg-card px-4 py-3.5" wire:key="p-{{ $permission->id }}">
                        <p class="font-medium tracking-tight">{{ $permission->display_name }}</p>
                        <p class="text-xs text-muted-foreground">{{ $permission->name }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <x-slate::alert variant="info" title="No permissions" description="Run php artisan electrik:permissions:sync" />
    @endforelse
</div>
