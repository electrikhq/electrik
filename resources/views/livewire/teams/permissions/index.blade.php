<div class="space-y-6">
    <x-electrik::page-header
        title="{{ __('Permissions') }}"
        :description="__('Catalog for :team. Managed via config + electrik:permissions:sync.', ['team' => $team->name])"
    >
        <x-slot:actions>
            <x-slate::button as="a" href="{{ route('teams.roles.index', $team) }}" variant="outline" wire:navigate>{{ __('Back to roles') }}</x-slate::button>
        </x-slot:actions>
    </x-electrik::page-header>

    @forelse ($permissions as $category => $items)
        <div class="space-y-2">
            <h2 class="text-sm font-medium text-muted-foreground">{{ $category ?: __('General') }}</h2>
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
        <x-slate::alert variant="info" title="{{ __('No permissions') }}" description="{{ __('Run php artisan electrik:permissions:sync') }}" />
    @endforelse
</div>
