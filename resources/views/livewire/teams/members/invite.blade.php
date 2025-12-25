<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Invite Team Member</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-1">{{ $team->name }}</p>
    </div>

    @if(session('message'))
        <x-slate::alert type="success" class="mb-6">
            {{ session('message') }}
        </x-slate::alert>
    @endif

    <x-slate::card>
        <x-slate::form wire:submit="invite">
            <x-slate::input 
                name="email" 
                type="email"
                label="Email Address"
                wire:model="email"
                placeholder="Enter email address"
                required
            />

            @if($roles->count() > 0)
                <x-slate::select 
                    name="role_id" 
                    label="Role (Optional)"
                    wire:model="role_id"
                    :options="$roles->pluck('display_name', 'id')->toArray()"
                    placeholder="Select a role"
                />
            @endif

            <div class="mt-4 p-4 bg-neutral-50 dark:bg-neutral-800 rounded-lg">
                <p class="text-sm text-neutral-600 dark:text-neutral-400">
                    An invitation email will be sent to the user. They can accept the invitation by clicking the link in the email.
                </p>
            </div>

            <div class="flex gap-3 mt-6">
                <x-slate::button type="submit" color="primary">Send Invitation</x-slate::button>
                <a href="{{ route('teams.members.index', $team) }}">
                    <x-slate::button variant="outline">Cancel</x-slate::button>
                </a>
            </div>
        </x-slate::form>
    </x-slate::card>
</div>

