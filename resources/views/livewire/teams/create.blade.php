<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Create Team</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-1">Create a new team to collaborate with others</p>
    </div>

    <x-slate::form wire:submit="create">
        <x-slate::input 
            name="name" 
            label="Team Name"
            wire:model="name"
            placeholder="Enter team name"
            required
        />

        <div class="flex gap-3 mt-6">
            <x-slate::button type="submit" color="primary">Create Team</x-slate::button>
            <a href="{{ route('teams.index') }}">
                <x-slate::button variant="outline">Cancel</x-slate::button>
            </a>
        </div>
    </x-slate::form>
</div>

