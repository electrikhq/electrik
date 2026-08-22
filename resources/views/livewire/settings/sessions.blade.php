<div class="mx-auto max-w-lg space-y-6">
    <x-electrik::page-header
        title="Sessions"
        description="Manage devices signed in to your account."
    />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif

    @unless ($usesDatabaseSessions)
        <x-slate::alert variant="warning" title="Database sessions recommended">
            Set <code class="text-xs">SESSION_DRIVER=database</code> in your <code class="text-xs">.env</code> to list and revoke sessions here.
        </x-slate::alert>
    @endunless

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-header>
            <x-slate::card-title>Active sessions</x-slate::card-title>
        </x-slate::card-header>
        <x-slate::card-content class="space-y-3">
            @forelse ($sessions as $entry)
                <div
                    class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-border/80 px-4 py-3"
                    wire:key="session-{{ $entry->id }}"
                >
                    <div class="min-w-0">
                        <p class="font-medium">
                            {{ $entry->is_current ? 'This device' : 'Other device' }}
                        </p>
                        <p class="truncate text-xs text-muted-foreground">
                            {{ $entry->ip_address ?: 'Unknown IP' }}
                            · {{ $entry->user_agent ? \Illuminate\Support\Str::limit($entry->user_agent, 60) : 'Unknown browser' }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            Last active {{ \Illuminate\Support\Carbon::createFromTimestamp($entry->last_activity)->diffForHumans() }}
                        </p>
                    </div>
                    @unless ($entry->is_current)
                        <x-slate::button
                            type="button"
                            variant="outline"
                            size="sm"
                            wire:click="logoutSession('{{ $entry->id }}')"
                        >
                            Revoke
                        </x-slate::button>
                    @endunless
                </div>
            @empty
                <p class="text-sm text-muted-foreground">No session records yet.</p>
            @endforelse
        </x-slate::card-content>
    </x-slate::card>

    @if ($sessions->where('is_current', false)->isNotEmpty())
        <x-slate::card class="border-border/80 shadow-xs">
            <x-slate::card-header>
                <x-slate::card-title>Sign out other devices</x-slate::card-title>
            </x-slate::card-header>
            <x-slate::card-content>
                <x-slate::form wire:submit="logoutOtherSessions" class="space-y-4">
                    <x-slate::input
                        wire:model="password"
                        type="password"
                        label="Confirm password"
                        autocomplete="current-password"
                        required
                    />
                    <x-slate::button type="submit" variant="destructive">Sign out other sessions</x-slate::button>
                </x-slate::form>
            </x-slate::card-content>
        </x-slate::card>
    @endif
</div>
