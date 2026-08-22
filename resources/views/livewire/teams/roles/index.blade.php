<div class="space-y-6">
    <x-electrik::page-header title="Roles" :description="$team->name">
        <x-slot:actions>
            <x-slate::button as="a" href="{{ route('teams.roles.create', $team) }}" wire:navigate>New role</x-slate::button>
            <x-slate::button as="a" href="{{ route('teams.permissions.index', $team) }}" variant="outline" wire:navigate>Permissions</x-slate::button>
        </x-slot:actions>
    </x-electrik::page-header>

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif
    @if (session('error'))
        <x-slate::alert variant="destructive" :title="session('error')" />
    @endif

    <div class="space-y-2">
        @foreach ($roles as $role)
            <div
                class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-border/80 bg-card px-4 py-3.5 transition-colors hover:bg-accent/30"
                wire:key="role-{{ $role->id }}"
            >
                <div>
                    <p class="font-medium tracking-tight">{{ $role->display_name }}</p>
                    <p class="text-xs text-muted-foreground">{{ $role->name }} · {{ $role->permissions->count() }} permissions</p>
                </div>
                <div class="flex gap-2">
                    <x-slate::button as="a" href="{{ route('teams.roles.edit', [$team, $role]) }}" variant="outline" size="sm" wire:navigate>
                        Edit
                    </x-slate::button>
                    @unless ($role->isSystem())
                        <x-electrik::confirm
                            title="Delete this role?"
                            description="Members using this role must be reassigned first."
                            confirm-label="Delete"
                            wire-click="delete({{ $role->id }})"
                        >
                            <x-slate::button type="button" variant="ghost" size="sm">
                                Delete
                            </x-slate::button>
                        </x-electrik::confirm>
                    @endunless
                </div>
            </div>
        @endforeach
    </div>
</div>
