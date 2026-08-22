<div class="mx-auto max-w-lg space-y-6">
    <x-electrik::page-header
        title="Profile"
        description="Update your name, email, timezone, and photo."
    />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-header>
            <x-slate::card-title>Profile photo</x-slate::card-title>
        </x-slate::card-header>
        <x-slate::card-content class="space-y-4">
            @php($user = auth()->user())
            @php($initials = collect(preg_split('/\s+/', trim((string) $user->name)) ?: [])->filter()->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->take(2)->implode(''))
            <div class="flex items-center gap-4">
                <x-slate::avatar size="lg" :src="$this->avatarUrl()" :fallback="$initials" :alt="$user->name" />
                @if ($user->avatar_path ?? null)
                    <x-slate::button type="button" variant="outline" wire:click="removeAvatar">Remove</x-slate::button>
                @endif
            </div>
            <x-slate::form wire:submit="updateAvatar" class="space-y-4">
                <input type="file" wire:model="avatar" accept="image/*" class="block w-full text-sm" />
                @error('avatar') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                <x-slate::button type="submit" wire:loading.attr="disabled">Upload photo</x-slate::button>
            </x-slate::form>
        </x-slate::card-content>
    </x-slate::card>

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-content>
            <x-slate::form wire:submit="save" class="space-y-4">
                <x-slate::input wire:model="name" label="Name" autocomplete="name" required />
                <x-slate::input wire:model="email" type="email" label="Email" autocomplete="email" required />

                <x-slate::select wire:model="timezone" label="Timezone" required>
                    @foreach ($timezones as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-slate::select>

                <x-slate::button type="submit" size="lg" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Save</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </x-slate::button>
            </x-slate::form>
        </x-slate::card-content>
    </x-slate::card>
</div>
