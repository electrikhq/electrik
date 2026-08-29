<div class="mx-auto max-w-lg space-y-6">
    <x-electrik::page-header
        title="{{ __('Sessions') }}"
        description="{{ __('Manage devices signed in to your account.') }}"
    />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif

    @unless ($usesDatabaseSessions)
        <x-slate::alert variant="warning" title="{{ __('Database sessions recommended') }}">
            {!! __('Set <code class="text-xs">SESSION_DRIVER=database</code> in your <code class="text-xs">.env</code> to list and revoke sessions here.') !!}
        </x-slate::alert>
    @endunless

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-header>
            <x-slate::card-title>{{ __('Active sessions') }}</x-slate::card-title>
        </x-slate::card-header>
        <x-slate::card-content class="space-y-3">
            @forelse ($sessions as $entry)
                <div
                    class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-border/80 px-4 py-3"
                    wire:key="session-{{ $entry->id }}"
                >
                    <div class="min-w-0 flex-1">
                        <p class="font-medium">
                            @if ($entry->label)
                                {{ $entry->label }}
                                @if ($entry->is_current)
                                    <span class="text-xs font-normal text-muted-foreground">({{ __('this device') }})</span>
                                @endif
                            @else
                                {{ $entry->is_current ? __('This device') : __('Other device') }}
                            @endif
                        </p>
                        <p class="truncate text-xs text-muted-foreground">
                            {{ $entry->ip_address ?: __('Unknown IP') }}
                            · {{ $entry->user_agent ? \Illuminate\Support\Str::limit($entry->user_agent, 60) : __('Unknown browser') }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            {{ __('Last active :time', ['time' => \Illuminate\Support\Carbon::createFromTimestamp($entry->last_activity)->diffForHumans()]) }}
                        </p>

                        @if ($renamingSessionId === $entry->id)
                            <div class="mt-2 flex flex-wrap items-end gap-2">
                                <div class="min-w-40 flex-1">
                                    <x-slate::input
                                        wire:model="sessionLabel"
                                        label="{{ __('Device name') }}"
                                        placeholder="{{ __('e.g. Office MacBook') }}"
                                    />
                                </div>
                                <x-slate::button type="button" size="sm" wire:click="saveSessionLabel">{{ __('Save') }}</x-slate::button>
                                <x-slate::button type="button" size="sm" variant="ghost" wire:click="$set('renamingSessionId', '')">{{ __('Cancel') }}</x-slate::button>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <x-slate::button
                            type="button"
                            variant="outline"
                            size="sm"
                            wire:click="startRename('{{ $entry->id }}')"
                        >
                            {{ __('Rename') }}
                        </x-slate::button>
                        @unless ($entry->is_current)
                            <x-slate::button
                                type="button"
                                variant="outline"
                                size="sm"
                                wire:click="logoutSession('{{ $entry->id }}')"
                            >
                                {{ __('Revoke') }}
                            </x-slate::button>
                        @endunless
                    </div>
                </div>
            @empty
                <p class="text-sm text-muted-foreground">{{ __('No session records yet.') }}</p>
            @endforelse
        </x-slate::card-content>
    </x-slate::card>

    @if ($sessions->where('is_current', false)->isNotEmpty())
        <x-slate::card class="border-border/80 shadow-xs">
            <x-slate::card-header>
                <x-slate::card-title>{{ __('Sign out other devices') }}</x-slate::card-title>
            </x-slate::card-header>
            <x-slate::card-content>
                <x-slate::form wire:submit="logoutOtherSessions" class="space-y-4">
                    <x-slate::input
                        wire:model="password"
                        type="password"
                        label="{{ __('Confirm password') }}"
                        autocomplete="current-password"
                        required
                    />
                    <x-slate::button type="submit" variant="destructive">{{ __('Sign out other sessions') }}</x-slate::button>
                </x-slate::form>
            </x-slate::card-content>
        </x-slate::card>
    @endif

    @if ($authLogEnabled)
        <x-slate::card class="border-border/80 shadow-xs">
            <x-slate::card-header>
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <x-slate::card-title>{{ __('Authentication log') }}</x-slate::card-title>
                        <x-slate::card-description>
                            {{ __('Recent sign-ins and failed attempts for this account.') }}
                        </x-slate::card-description>
                    </div>
                    <x-slate::select wire:model.live="authFilter" class="h-9 w-auto min-w-36">
                        <option value="">{{ __('All events') }}</option>
                        <option value="success">{{ __('Signed in') }}</option>
                        <option value="failed">{{ __('Failed sign-in') }}</option>
                        <option value="suspicious">{{ __('Suspicious') }}</option>
                    </x-slate::select>
                </div>
            </x-slate::card-header>
            <x-slate::card-content class="space-y-3">
                @forelse ($authenticationLogs as $log)
                    <div
                        class="rounded-xl border border-border/80 px-4 py-3"
                        wire:key="auth-log-{{ $log->id }}"
                    >
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="font-medium">
                                @if ($log->login_successful)
                                    {{ __('Signed in') }}
                                @else
                                    {{ __('Failed sign-in') }}
                                @endif
                            </p>
                            <div class="flex flex-wrap gap-1">
                                @if ($log->is_suspicious)
                                    <x-slate::badge variant="destructive">{{ __('Suspicious') }}</x-slate::badge>
                                @endif
                                @if ($log->logout_at)
                                    <x-slate::badge variant="secondary">{{ __('Signed out') }}</x-slate::badge>
                                @endif
                            </div>
                        </div>
                        <p class="mt-1 truncate text-xs text-muted-foreground">
                            {{ $log->ip_address ?: __('Unknown IP') }}
                            @if ($log->device_name)
                                · {{ $log->device_name }}
                            @elseif ($log->user_agent)
                                · {{ \Illuminate\Support\Str::limit($log->user_agent, 48) }}
                            @endif
                        </p>
                        <p class="text-xs text-muted-foreground">
                            {{ $log->login_at?->diffForHumans() ?? __('Unknown time') }}
                            @if ($log->logout_at)
                                · {{ __('Left :time', ['time' => $log->logout_at->diffForHumans()]) }}
                            @endif
                        </p>
                        @if ($log->is_suspicious && $log->suspicious_reason)
                            <p class="mt-1 text-xs text-destructive">{{ $log->suspicious_reason }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-muted-foreground">{{ __('No authentication events yet.') }}</p>
                @endforelse

                @if ($authenticationLogs instanceof \Illuminate\Contracts\Pagination\Paginator)
                    <div class="pt-2">
                        {{ $authenticationLogs->links() }}
                    </div>
                @endif
            </x-slate::card-content>
        </x-slate::card>
    @endif
</div>
