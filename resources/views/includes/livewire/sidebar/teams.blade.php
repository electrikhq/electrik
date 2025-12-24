<div class="flex flex-col items-start w-full px-6 py-6 h-full">
    <h2 class="text-xl font-medium items-center flex space-x-4">Teams</h2>
    
    @php
        $currentRouteName = Route::currentRouteName();
    @endphp

    <div class="mt-6 w-full">
        <h3 class="text-sm font-semibold text-neutral-500 dark:text-neutral-400 uppercase mb-3">Team</h3>
        <a class="hover:underline block mt-2 {{ $currentRouteName === 'teams.index' ? 'underline text-primary-600 dark:text-primary-700' : '' }}" href="{{ route('teams.index') }}">All Teams</a>
        <a class="hover:underline block mt-3 {{ $currentRouteName === 'teams.create' ? 'underline text-primary-600 dark:text-primary-700' : '' }}" href="{{ route('teams.create') }}">Create Team</a>
        @if(auth()->user()->currentTeam)
            <a class="hover:underline block mt-3 {{ $currentRouteName === 'teams.settings' ? 'underline text-primary-600 dark:text-primary-700' : '' }}" href="{{ route('teams.settings', auth()->user()->currentTeam) }}">Team Settings</a>
            <a class="hover:underline block mt-3 {{ $currentRouteName === 'teams.members.index' ? 'underline text-primary-600 dark:text-primary-700' : '' }}" href="{{ route('teams.members.index', auth()->user()->currentTeam) }}">Members</a>
        @endif
    </div>
</div>

