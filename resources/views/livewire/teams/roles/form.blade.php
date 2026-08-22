<div class="mx-auto max-w-2xl space-y-6">
    <x-electrik::page-header
        :title="$editing ? 'Edit role' : 'Create role'"
        :description="$team->name"
    />

    @if (session('error'))
        <x-slate::alert variant="destructive" :title="session('error')" />
    @endif

    @if ($locked)
        <x-slate::alert variant="info" title="Owner role" description="Owner always has all permissions and cannot be edited." />
    @else
        <x-slate::card class="border-border/80 shadow-xs">
            <x-slate::card-content>
                <x-slate::form wire:submit="save" class="space-y-6">
                    <x-slate::input wire:model="name" label="Name" :disabled="$system ?? false" required />
                    <x-slate::input wire:model="display_name" label="Display name" />

                    <div class="space-y-4">
                        <h2 class="text-sm font-medium">Permissions</h2>
                        @foreach ($permissions as $category => $items)
                            <div class="space-y-2 rounded-xl border border-border/80 p-4">
                                <p class="text-sm font-medium">{{ $category ?: 'General' }}</p>
                                @foreach ($items as $permission)
                                    <label class="flex items-center gap-2 text-sm" wire:key="perm-{{ $permission->id }}">
                                        <input type="checkbox" value="{{ $permission->name }}" wire:model="selectedPermissions" class="rounded border-input" />
                                        <span>{{ $permission->display_name }}</span>
                                        <span class="text-xs text-muted-foreground">{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    <x-slate::button type="submit" size="lg">Save</x-slate::button>
                </x-slate::form>
            </x-slate::card-content>
        </x-slate::card>
    @endif
</div>
