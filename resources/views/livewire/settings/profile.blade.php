<div class="mx-auto max-w-lg space-y-6">
    <x-electrik::page-header
        title="{{ __('Profile') }}"
        description="{{ __('Update your name, email, language, timezone, and photo.') }}"
    />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-header>
            <x-slate::card-title>{{ __('Profile photo') }}</x-slate::card-title>
        </x-slate::card-header>
        <x-slate::card-content class="space-y-4">
            @php($user = auth()->user())
            @php($initials = collect(preg_split('/\s+/', trim((string) $user->name)) ?: [])->filter()->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->take(2)->implode(''))
            <div class="flex items-center gap-4">
                <x-slate::avatar size="lg" :src="$this->avatarUrl()" :fallback="$initials" :alt="$user->name" />
                @if ($user->avatar_path ?? null)
                    <x-slate::button type="button" variant="outline" wire:click="removeAvatar">{{ __('Remove') }}</x-slate::button>
                @endif
            </div>
            <x-slate::form wire:submit="updateAvatar" class="space-y-4">
                <input type="file" wire:model="avatar" accept="image/*" class="block w-full text-sm" />
                @error('avatar') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                <x-slate::button type="submit" wire:loading.attr="disabled">{{ __('Upload photo') }}</x-slate::button>
            </x-slate::form>
        </x-slate::card-content>
    </x-slate::card>

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-content>
            <x-slate::form wire:submit="save" class="space-y-4">
                <x-slate::input wire:model="name" label="{{ __('Name') }}" autocomplete="name" required />
                <x-slate::input wire:model="email" type="email" label="{{ __('Email') }}" autocomplete="email" required />

                <x-slate::select wire:model="locale" label="{{ __('Language') }}" required>
                    @foreach ($locales as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-slate::select>

                <x-slate::select wire:model="timezone" label="{{ __('Timezone') }}" required>
                    @foreach ($timezones as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-slate::select>

                <x-slate::button type="submit" size="lg" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">{{ __('Save') }}</span>
                    <span wire:loading wire:target="save">{{ __('Saving…') }}</span>
                </x-slate::button>
            </x-slate::form>
        </x-slate::card-content>
    </x-slate::card>

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-header>
            <x-slate::card-title>{{ __('Privacy') }}</x-slate::card-title>
            <x-slate::card-description>
                {{ __('Export a copy of your personal data or permanently delete your account.') }}
            </x-slate::card-description>
        </x-slate::card-header>
        <x-slate::card-content class="space-y-6">
            @if ($personalDataExportEnabled)
                <div class="space-y-3">
                    <p class="text-sm text-muted-foreground">
                        {{ __('We build the archive in the background and email you a secure download link. Links expire after a few days.') }}
                    </p>
                    <x-slate::button type="button" variant="outline" wire:click="requestDataExport" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="requestDataExport">{{ __('Email me my data export') }}</span>
                        <span wire:loading wire:target="requestDataExport">{{ __('Queuing…') }}</span>
                    </x-slate::button>
                </div>
            @endif

            <div class="space-y-3 border-t border-border/80 pt-6">
                <p class="text-sm text-muted-foreground">
                    {{ __('Deleting your account is permanent. You cannot delete your account while you own a team that still has other members.') }}
                </p>
                <div class="space-y-4">
                    <x-slate::input
                        wire:model="delete_password"
                        type="password"
                        label="{{ __('Confirm password') }}"
                        autocomplete="current-password"
                        required
                    />
                    <x-electrik::confirm
                        :title="__('Delete your account permanently?')"
                        :description="__('This cannot be undone.')"
                        :confirm-label="__('Delete account')"
                        wire-click="deleteAccount"
                    >
                        <x-slate::button type="button" variant="destructive" wire:loading.attr="disabled">
                            {{ __('Delete account') }}
                        </x-slate::button>
                    </x-electrik::confirm>
                </div>
            </div>
        </x-slate::card-content>
    </x-slate::card>
</div>
