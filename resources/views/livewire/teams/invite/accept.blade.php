<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Accept Team Invitation</h1>
        <p class="text-neutral-600 dark:text-neutral-400 mt-1">You've been invited to join a team</p>
    </div>

    @if(session('error'))
        <x-slate::alert type="error" class="mb-6">
            {{ session('error') }}
        </x-slate::alert>
    @endif

    @if(session('message'))
        <x-slate::alert type="success" class="mb-6">
            {{ session('message') }}
        </x-slate::alert>
    @endif

    @if(!session('error') && !session('message'))
        <x-slate::card>
            <div class="text-center py-8">
                <h3 class="text-lg font-semibold mb-2">You've been invited to join</h3>
                <p class="text-2xl font-bold text-primary-600 dark:text-primary-400 mb-6">{{ $invite->team->name }}</p>
                
                <p class="text-neutral-600 dark:text-neutral-400 mb-6">
                    Click the button below to accept the invitation and join the team.
                </p>

                <form wire:submit="accept">
                    <x-slate::button type="submit" color="primary" size="lg">Accept Invitation</x-slate::button>
                </form>
            </div>
        </x-slate::card>
    @endif
</div>

