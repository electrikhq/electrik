<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Teams</h1>
            <p class="text-neutral-600 dark:text-neutral-400 mt-1">Manage your teams</p>
        </div>
        <a href="{{ route('teams.create') }}">
            <x-slate::button color="primary">Create Team</x-slate::button>
        </a>
    </div>

    @if(session('message'))
        <x-slate::alert type="success" class="mb-6">
            {{ session('message') }}
        </x-slate::alert>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach(auth()->user()->teams as $team)
            <x-slate::card>
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $team->name }}</h3>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                            {{ $team->users()->count() }} {{ Str::plural('member', $team->users()->count()) }}
                        </p>
                    </div>
                    @if(auth()->user()->current_team_id === $team->id)
                        <x-slate::badge color="primary">Current</x-slate::badge>
                    @endif
                </div>
                
                <div class="flex gap-2 mt-4">
                    @if(auth()->user()->current_team_id !== $team->id)
                        <form method="POST" action="{{ route('teams.switch', $team) }}">
                            @csrf
                            <x-slate::button type="submit" size="sm" variant="outline">Switch</x-slate::button>
                        </form>
                    @endif
                    <a href="{{ route('teams.settings', $team) }}">
                        <x-slate::button size="sm" variant="outline">Settings</x-slate::button>
                    </a>
                    <a href="{{ route('teams.members.index', $team) }}">
                        <x-slate::button size="sm" variant="outline">Members</x-slate::button>
                    </a>
                </div>
            </x-slate::card>
        @endforeach
    </div>

    @if(auth()->user()->teams()->count() === 0)
        <x-slate::card>
            <div class="text-center py-12">
                <p class="text-neutral-600 dark:text-neutral-400 mb-4">You don't have any teams yet.</p>
                <a href="{{ route('teams.create') }}">
                    <x-slate::button color="primary">Create Your First Team</x-slate::button>
                </a>
            </div>
        </x-slate::card>
    @endif
</div>

