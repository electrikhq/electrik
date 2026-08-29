<div class="space-y-6">
    <x-electrik::page-header
        title="{{ __('Members') }}"
        :description="$team->name"
    >
        <x-slot:actions>
            @if ($canManageMembers)
                <x-slate::button as="a" href="{{ route('teams.members.invite', $team) }}" wire:navigate>
                    {{ __('Invite') }}
                </x-slate::button>
            @endif
            @if ((int) $team->owner_id !== (int) auth()->id())
                <x-electrik::confirm
                    :title="__('Leave this team?')"
                    :description="__('You will lose access until you are invited again.')"
                    :confirm-label="__('Leave team')"
                    wire-click="leave"
                >
                    <x-slate::button type="button" variant="outline">
                        {{ __('Leave team') }}
                    </x-slate::button>
                </x-electrik::confirm>
            @endif
        </x-slot:actions>
    </x-electrik::page-header>

    @error('leave')
        <x-slate::alert variant="destructive" :title="$message" />
    @enderror

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif

    <div class="space-y-2">
        @foreach ($members as $member)
            <div
                class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-border/80 bg-card px-4 py-3.5 transition-colors hover:bg-accent/30"
                wire:key="member-{{ $member->id }}"
            >
                <div>
                    <p class="font-medium tracking-tight">{{ $member->name }}</p>
                    <p class="text-xs text-muted-foreground">{{ $member->email }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if ((int) $team->owner_id === (int) $member->id)
                        <x-slate::badge>{{ __('Owner') }}</x-slate::badge>
                    @elseif ($canManageMembers)
                        <x-slate::select
                            wire:change="updateRole({{ $member->id }}, $event.target.value)"
                            class="h-8 w-auto min-w-28"
                        >
                            @foreach ($assignableRoles as $roleName)
                                <option value="{{ $roleName }}" @selected($member->electrik_role === $roleName)>
                                    {{ ucfirst($roleName) }}
                                </option>
                            @endforeach
                        </x-slate::select>
                        @if ($canImpersonate && (int) $member->id !== (int) auth()->id())
                            <x-slate::button type="button" variant="outline" size="sm" wire:click="impersonate({{ $member->id }})">
                                {{ __('Impersonate') }}
                            </x-slate::button>
                        @endif
                        <x-electrik::confirm
                            :title="__('Remove this member?')"
                            :description="__('They will lose access to :team.', ['team' => $team->name])"
                            :confirm-label="__('Remove')"
                            wire-click="remove({{ $member->id }})"
                        >
                            <x-slate::button type="button" variant="ghost" size="sm">
                                {{ __('Remove') }}
                            </x-slate::button>
                        </x-electrik::confirm>
                    @else
                        <x-slate::badge variant="secondary">{{ ucfirst($member->electrik_role ?? 'member') }}</x-slate::badge>
                        @if ($canImpersonate && (int) $member->id !== (int) auth()->id() && (int) $team->owner_id !== (int) $member->id)
                            <x-slate::button type="button" variant="outline" size="sm" wire:click="impersonate({{ $member->id }})">
                                {{ __('Impersonate') }}
                            </x-slate::button>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if ($invitations->isNotEmpty())
        <div class="space-y-2">
            <h2 class="text-sm font-medium text-muted-foreground">{{ __('Pending invitations') }}</h2>
            @foreach ($invitations as $invitation)
                <div
                    class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-dashed border-border bg-card/50 px-4 py-3.5"
                    wire:key="invite-{{ $invitation->id }}"
                >
                    <div>
                        <p class="font-medium tracking-tight">{{ $invitation->email }}</p>
                        <p class="text-xs text-muted-foreground">
                            {{ $invitation->role ? ucfirst($invitation->role) : __('Member') }}
                            @if ($invitation->expires_at)
                                · {{ __('expires :date', ['date' => \Illuminate\Support\Carbon::parse($invitation->expires_at)->diffForHumans()]) }}
                            @endif
                        </p>
                    </div>
                    @if ($canManageMembers)
                        <div class="flex gap-2">
                            <x-slate::button type="button" variant="outline" size="sm" wire:click="resendInvite({{ $invitation->id }})">
                                {{ __('Resend') }}
                            </x-slate::button>
                            <x-electrik::confirm
                                :title="__('Cancel this invitation?')"
                                :description="__(':email will no longer be able to join with this link.', ['email' => $invitation->email])"
                                :confirm-label="__('Cancel invitation')"
                                wire-click="cancelInvite({{ $invitation->id }})"
                            >
                                <x-slate::button type="button" variant="ghost" size="sm">
                                    {{ __('Cancel') }}
                                </x-slate::button>
                            </x-electrik::confirm>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
