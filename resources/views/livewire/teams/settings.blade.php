<div class="mx-auto max-w-lg space-y-6">
    <x-electrik::page-header
        title="{{ __('Team settings') }}"
        :description="$team->name"
    />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-content>
            <x-slate::form wire:submit="save" class="space-y-4">
                <x-slate::input wire:model="name" label="{{ __('Team name') }}" required />
                <x-slate::button type="submit" size="lg">{{ __('Save') }}</x-slate::button>
            </x-slate::form>
        </x-slate::card-content>
    </x-slate::card>

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-header>
            <x-slate::card-title>{{ __('Team avatar') }}</x-slate::card-title>
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
                    <x-slate::button type="button" variant="outline" wire:click="removeAvatar">{{ __('Remove') }}</x-slate::button>
                @endif
            </div>

            <x-slate::form wire:submit="updateAvatar" class="space-y-4">
                <input type="file" wire:model="avatar" accept="image/*" class="block w-full text-sm" />
                @error('avatar') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                <x-slate::button type="submit" wire:loading.attr="disabled">{{ __('Upload avatar') }}</x-slate::button>
            </x-slate::form>
        </x-slate::card-content>
    </x-slate::card>

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-header>
            <x-slate::card-title>{{ __('Branding') }}</x-slate::card-title>
            <x-slate::card-description>
                {{ __('White-label the app shell for this team. Falls back to your app defaults when unset.') }}
            </x-slate::card-description>
        </x-slate::card-header>
        <x-slate::card-content class="space-y-6">
            <div class="space-y-3">
                <p class="text-sm font-medium">{{ __('Brand logo') }}</p>
                <div class="flex items-center gap-4">
                    <div class="flex size-12 items-center justify-center overflow-hidden rounded-lg border border-border/80 bg-muted">
                        @if ($team->brandLogoUrl())
                            <img src="{{ $team->brandLogoUrl() }}" alt="" class="size-full object-cover" />
                        @else
                            <span class="text-xs text-muted-foreground">{{ __('None') }}</span>
                        @endif
                    </div>
                    @if ($team->brand_logo_path)
                        <x-slate::button type="button" variant="outline" size="sm" wire:click="removeBrandLogo">{{ __('Remove') }}</x-slate::button>
                    @endif
                </div>

                <x-slate::form wire:submit="updateBrandLogo" class="space-y-3">
                    <input type="file" wire:model="brandLogo" accept="image/*" class="block w-full text-sm" />
                    @error('brandLogo') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    <x-slate::button type="submit" size="sm" wire:loading.attr="disabled">{{ __('Upload logo') }}</x-slate::button>
                </x-slate::form>
            </div>

            <x-slate::form wire:submit="saveBranding" class="space-y-3">
                <div
                    class="flex flex-wrap items-end gap-3"
                    x-data="{
                        hex: @entangle('brandPrimary').live,
                        syncFromPicker(value) {
                            this.hex = value;
                        },
                        syncFromHex() {
                            if (this.hex && /^#[0-9A-Fa-f]{6}$/.test(this.hex)) {
                                $refs.picker.value = this.hex;
                            }
                        }
                    }"
                    x-init="syncFromHex()"
                >
                    <div class="space-y-2">
                        <x-slate::field-label for="brand-primary-picker">{{ __('Primary color') }}</x-slate::field-label>
                        <input
                            id="brand-primary-picker"
                            x-ref="picker"
                            type="color"
                            class="h-10 w-14 cursor-pointer rounded-md border border-border/80 bg-transparent p-1"
                            value="{{ $brandPrimary && preg_match('/^#[0-9A-Fa-f]{6}$/', $brandPrimary) ? $brandPrimary : '#4f46e5' }}"
                            x-on:input="syncFromPicker($event.target.value)"
                        />
                    </div>
                    <div class="min-w-40 flex-1">
                        <x-slate::input
                            wire:model.live="brandPrimary"
                            label="{{ __('Hex') }}"
                            placeholder="#4f46e5"
                            description="{{ __('Leave blank to use the app default.') }}"
                            x-on:blur="syncFromHex()"
                        />
                    </div>
                </div>
                <x-slate::button type="submit" size="sm">{{ __('Save branding') }}</x-slate::button>
            </x-slate::form>
        </x-slate::card-content>
    </x-slate::card>

    @if ($isOwner && $transferCandidates->isNotEmpty())
        <x-slate::card class="border-border/80 shadow-xs">
            <x-slate::card-header>
                <x-slate::card-title>{{ __('Transfer ownership') }}</x-slate::card-title>
                <x-slate::card-description>
                    {{ __('Make another member the owner. You will be demoted to the role you choose.') }}
                </x-slate::card-description>
            </x-slate::card-header>
            <x-slate::card-content>
                <div class="space-y-4">
                    <x-slate::select wire:model="transferToUserId" label="{{ __('New owner') }}" required>
                        <option value="">{{ __('Select a member…') }}</option>
                        @foreach ($transferCandidates as $candidate)
                            <option value="{{ $candidate->id }}">{{ $candidate->name }} ({{ $candidate->email }})</option>
                        @endforeach
                    </x-slate::select>

                    <x-slate::select wire:model="demoteRole" label="{{ __('Your new role') }}">
                        <option value="admin">{{ __('Admin') }}</option>
                        <option value="member">{{ __('Member') }}</option>
                    </x-slate::select>

                    <x-electrik::confirm
                        :title="__('Transfer ownership?')"
                        :description="__('You will no longer be the team owner. This cannot be undone from here.')"
                        :confirm-label="__('Transfer ownership')"
                        wire-click="transferOwnership"
                    >
                        <x-slate::button type="button" variant="outline">
                            {{ __('Transfer ownership') }}
                        </x-slate::button>
                    </x-electrik::confirm>
                </div>
            </x-slate::card-content>
        </x-slate::card>
    @endif

    @if ($isOwner)
        <x-slate::card class="border-border/80 shadow-xs">
            <x-slate::card-header>
                <x-slate::card-title>{{ __('Login IP allowlist') }}</x-slate::card-title>
                <x-slate::card-description>
                    {{ __('Optional. When set, members of this team must sign in from one of these IPs. Leave blank to allow any IP.') }}
                </x-slate::card-description>
            </x-slate::card-header>
            <x-slate::card-content>
                <x-slate::form wire:submit="saveAllowedIps" class="space-y-4">
                    <x-slate::textarea
                        wire:model="allowedIpsText"
                        label="{{ __('Allowed IPs') }}"
                        rows="4"
                        description="{{ __('One IP per line (IPv4 or IPv6).') }}"
                    />
                    <x-slate::button type="submit" size="sm">{{ __('Save allowlist') }}</x-slate::button>
                </x-slate::form>
            </x-slate::card-content>
        </x-slate::card>
    @endif

    @if ($isOwner)
        <x-slate::card class="border-destructive/30 shadow-xs">
            <x-slate::card-header>
                <x-slate::card-title class="text-destructive">{{ __('Danger zone') }}</x-slate::card-title>
                <x-slate::card-description>
                    {{ __('Archive hides the team from switchers. Delete permanently cancels billing and removes members.') }}
                </x-slate::card-description>
            </x-slate::card-header>
            <x-slate::card-content class="flex flex-wrap gap-3">
                @if ($team->isArchived())
                    <x-slate::button type="button" variant="outline" wire:click="restoreTeam">
                        {{ __('Restore team') }}
                    </x-slate::button>
                @else
                    <x-electrik::confirm
                        :title="__('Archive this team?')"
                        :description="__('Archived teams are hidden from the switcher. You can restore later.')"
                        :confirm-label="__('Archive team')"
                        wire-click="archiveTeam"
                    >
                        <x-slate::button type="button" variant="outline">
                            {{ __('Archive team') }}
                        </x-slate::button>
                    </x-electrik::confirm>
                @endif

                <x-electrik::confirm
                    :title="__('Delete this team?')"
                    :description="__('This permanently deletes :team, cancels billing, and removes all members. This cannot be undone.', ['team' => $team->name])"
                    :confirm-label="__('Delete team')"
                    wire-click="deleteTeam"
                >
                    <x-slate::button type="button" variant="destructive">
                        {{ __('Delete team') }}
                    </x-slate::button>
                </x-electrik::confirm>
            </x-slate::card-content>
        </x-slate::card>
    @endif
</div>
