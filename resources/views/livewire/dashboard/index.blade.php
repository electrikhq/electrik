<div>
    <x-slate::header class="border-b border-neutral-200 dark:border-neutral-800" full-width color="white" shadow>
        <x-slot name="title">
            <div>
                {{ Breadcrumbs::render('dashboard.index') }}
            </div>
            <div class="flex items-center space-x-2">
                <x-slate::icon icon="carbon-dashboard" color="black" />
                <h2 tag="h1" font-bold>
                    Dashboard
                </h2>
            </div>
        </x-slot>
    </x-slate::header>
    
    <div class="p-6">
        <x-slate::card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold">Welcome</h2>
            </x-slot>

            <div class="space-y-4">
                <p class="text-gray-600">Welcome to your dashboard!</p>
                
                @if(auth()->user()->currentTeam)
                    <div>
                        <p class="text-sm text-gray-500">Current Team: <strong>{{ auth()->user()->currentTeam->name }}</strong></p>
                    </div>
                @endif
            </div>
        </x-slate::card>
    </div>
</div>
