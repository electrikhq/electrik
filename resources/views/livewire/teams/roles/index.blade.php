<div class="space-y-6">
    <x-electrik::page-header title="{{ __('Roles') }}" :description="$team->name">
        <x-slot:actions>
            @if ($canCreateCustomRoles)
                <x-slate::button as="a" href="{{ route('teams.roles.create', $team) }}" wire:navigate>{{ __('New role') }}</x-slate::button>
            @endif
            <x-slate::button as="a" href="{{ route('teams.permissions.index', $team) }}" variant="outline" wire:navigate>{{ __('Permissions') }}</x-slate::button>
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
                    <p class="text-xs text-muted-foreground">{{ $role->name }} · {{ __(':count permissions', ['count' => $role->permissions->count()]) }}</p>
                </div>
                <div class="flex gap-2">
                    <x-slate::button as="a" href="{{ route('teams.roles.edit', [$team, $role]) }}" variant="outline" size="sm" wire:navigate>
                        {{ __('Edit') }}
                    </x-slate::button>
                    @unless ($role->isSystem())
                        <x-electrik::confirm
                            :title="__('Delete this role?')"
                            :description="__('Members using this role must be reassigned first.')"
                            :confirm-label="__('Delete')"
                            wire-click="delete({{ $role->id }})"
                        >
                            <x-slate::button type="button" variant="ghost" size="sm">
                                {{ __('Delete') }}
                            </x-slate::button>
                        </x-electrik::confirm>
                    @endunless
                </div>
            </div>
        @endforeach
    </div>
</div>
