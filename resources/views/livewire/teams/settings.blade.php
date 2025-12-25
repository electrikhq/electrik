<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Team Settings</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-1">Manage your team settings</p>
    </div>

    @if(session('message'))
        <x-slate::alert type="success" class="mb-6">
            {{ session('message') }}
        </x-slate::alert>
    @endif

    <x-slate::card>
        <x-slate::form wire:submit="update">
            <x-slate::input 
                name="name" 
                label="Team Name"
                wire:model="name"
                placeholder="Enter team name"
                required
            />

            <div class="flex gap-3 mt-6">
                <x-slate::button type="submit" color="primary">Update Settings</x-slate::button>
                <a href="{{ route('teams.index') }}">
                    <x-slate::button variant="outline">Back</x-slate::button>
                </a>
            </div>
        </x-slate::form>
    </x-slate::card>

    <x-slate::card class="mt-6">
        <h3 class="text-lg font-semibold mb-4">Team Information</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <dt class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Owner</dt>
                <dd class="mt-1">{{ $team->owner->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Members</dt>
                <dd class="mt-1">{{ $team->users()->count() }} {{ Str::plural('member', $team->users()->count()) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Created</dt>
                <dd class="mt-1">{{ $team->created_at->format('M d, Y') }}</dd>
            </div>
        </dl>
    </x-slate::card>
</div>

