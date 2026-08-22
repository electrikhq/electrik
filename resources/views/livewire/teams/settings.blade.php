<div class="mx-auto max-w-lg space-y-6">
    <x-electrik::page-header
        title="Team settings"
        :description="$team->name"
    />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-content>
            <x-slate::form wire:submit="save" class="space-y-4">
                <x-slate::input wire:model="name" label="Team name" required />
                <x-slate::button type="submit" size="lg">Save</x-slate::button>
            </x-slate::form>
        </x-slate::card-content>
    </x-slate::card>

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-header>
            <x-slate::card-title>Team avatar</x-slate::card-title>
        </x-slate::card-header>
        <x-slate::card-content class="space-y-4">
            <div class="flex items-center gap-4">
                <x-slate::avatar
                    size="lg"
                    :src="$team->avatarUrl()"
                    :fallback="mb_strtoupper(mb_substr($team->name, 0, 1))"
                    :alt="$team->name"
                />
                @if ($team->avatar_path)
                    <x-slate::button type="button" variant="outline" wire:click="removeAvatar">Remove</x-slate::button>
                @endif
            </div>

            <x-slate::form wire:submit="updateAvatar" class="space-y-4">
                <input type="file" wire:model="avatar" accept="image/*" class="block w-full text-sm" />
                @error('avatar') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                <x-slate::button type="submit" wire:loading.attr="disabled">Upload avatar</x-slate::button>
            </x-slate::form>
        </x-slate::card-content>
    </x-slate::card>

    @if ($isOwner && $transferCandidates->isNotEmpty())
        <x-slate::card class="border-border/80 shadow-xs">
            <x-slate::card-header>
                <x-slate::card-title>Transfer ownership</x-slate::card-title>
                <x-slate::card-description>
                    Make another member the owner. You will be demoted to the role you choose.
                </x-slate::card-description>
            </x-slate::card-header>
            <x-slate::card-content>
                <div class="space-y-4">
                    <x-slate::select wire:model="transferToUserId" label="New owner" required>
                        <option value="">Select a member…</option>
                        @foreach ($transferCandidates as $candidate)
                            <option value="{{ $candidate->id }}">{{ $candidate->name }} ({{ $candidate->email }})</option>
                        @endforeach
                    </x-slate::select>

                    <x-slate::select wire:model="demoteRole" label="Your new role">
                        <option value="admin">Admin</option>
                        <option value="member">Member</option>
                    </x-slate::select>

                    <x-electrik::confirm
                        title="Transfer ownership?"
                        description="You will no longer be the team owner. This cannot be undone from here."
                        confirm-label="Transfer ownership"
                        wire-click="transferOwnership"
                    >
                        <x-slate::button type="button" variant="outline">
                            Transfer ownership
                        </x-slate::button>
                    </x-electrik::confirm>
                </div>
            </x-slate::card-content>
        </x-slate::card>
    @endif

    @if ($isOwner)
        <x-slate::card class="border-destructive/30 shadow-xs">
            <x-slate::card-header>
                <x-slate::card-title class="text-destructive">Danger zone</x-slate::card-title>
                <x-slate::card-description>
                    Delete this team permanently. Subscriptions will be cancelled, members removed, and roles deleted.
                </x-slate::card-description>
            </x-slate::card-header>
            <x-slate::card-content>
                <x-electrik::confirm
                    title="Delete this team?"
                    description="This permanently deletes {{ $team->name }}, cancels billing, and removes all members. This cannot be undone."
                    confirm-label="Delete team"
                    wire-click="deleteTeam"
                >
                    <x-slate::button type="button" variant="destructive">
                        Delete team
                    </x-slate::button>
                </x-electrik::confirm>
            </x-slate::card-content>
        </x-slate::card>
    @endif
</div>
