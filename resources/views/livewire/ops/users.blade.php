<div class="space-y-6">
    <x-electrik::page-header
        title="{{ __('Users') }}"
        description="{{ __('Search accounts, suspend access, or impersonate for support.') }}"
    />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif

    <x-slate::input
        wire:model.live.debounce.400ms="search"
        type="search"
        placeholder="{{ __('Search by name or email') }}"
        class="max-w-sm"
    />

    <div class="space-y-2">
        @forelse ($users as $user)
            <div
                class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-border/80 bg-card px-4 py-3.5 transition-colors hover:bg-accent/30"
                wire:key="ops-user-{{ $user->id }}"
            >
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="font-medium tracking-tight">{{ $user->name }}</p>
                        @if ($suspendable && $user->suspended_at)
                            <x-slate::badge variant="destructive">{{ __('Suspended') }}</x-slate::badge>
                        @endif
                    </div>
                    <p class="text-xs text-muted-foreground">{{ $user->email }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($canImpersonate && (int) $user->id !== (int) auth()->id())
                        <x-slate::button type="button" variant="outline" size="sm" wire:click="impersonate({{ $user->id }})">
                            {{ __('Impersonate') }}
                        </x-slate::button>
                    @endif

                    @if ($suspendable && (int) $user->id !== (int) auth()->id())
                        @if ($user->suspended_at)
                            <x-electrik::confirm
                                :title="__('Unsuspend this user?')"
                                :description="__('They will be able to sign in again immediately.')"
                                :confirm-label="__('Unsuspend')"
                                confirm-variant="default"
                                wire-click="unsuspend({{ $user->id }})"
                            >
                                <x-slate::button type="button" variant="outline" size="sm">
                                    {{ __('Unsuspend') }}
                                </x-slate::button>
                            </x-electrik::confirm>
                        @else
                            <x-electrik::confirm
                                :title="__('Suspend this user?')"
                                :description="__('They will be signed out and unable to sign in until unsuspended.')"
                                :confirm-label="__('Suspend')"
                                wire-click="suspend({{ $user->id }})"
                            >
                                <x-slate::button type="button" variant="ghost" size="sm">
                                    {{ __('Suspend') }}
                                </x-slate::button>
                            </x-electrik::confirm>
                        @endif
                    @endif
                </div>
            </div>
        @empty
            <x-slate::alert variant="info" :title="__('No users found')" />
        @endforelse
    </div>

    <div>
        {{ $users->links() }}
    </div>
</div>
