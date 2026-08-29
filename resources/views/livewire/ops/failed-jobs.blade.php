<div class="space-y-6">
    <x-electrik::page-header
        title="{{ __('Failed jobs') }}"
        description="{{ __('Queue jobs that threw an exception. Retry once the underlying issue is fixed.') }}"
    >
        @if ($tableExists && $jobs->total() > 0)
            <x-slot:actions>
                <x-electrik::confirm
                    :title="__('Retry all failed jobs?')"
                    :description="__('Every job below is pushed back onto its original queue.')"
                    :confirm-label="__('Retry all')"
                    confirm-variant="default"
                    wire-click="retryAll"
                >
                    <x-slate::button type="button" variant="outline">
                        {{ __('Retry all') }}
                    </x-slate::button>
                </x-electrik::confirm>
            </x-slot:actions>
        @endif
    </x-electrik::page-header>

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif

    @unless ($tableExists)
        <x-slate::alert
            variant="info"
            :title="__('No failed jobs table')"
            :description="__('Run your queue failed-jobs migration to track failed jobs here.')"
        />
    @else
        <div class="space-y-2">
            @forelse ($jobs as $job)
                <div
                    class="flex flex-wrap items-start justify-between gap-3 rounded-xl border border-border/80 bg-card px-4 py-3.5"
                    wire:key="failed-job-{{ $job->id }}"
                >
                    <div class="min-w-0 space-y-1">
                        <div class="flex items-center gap-2">
                            <p class="font-medium tracking-tight">{{ $job->queue }}</p>
                            <x-slate::badge variant="outline">{{ $job->connection }}</x-slate::badge>
                        </div>
                        <p class="truncate text-xs text-muted-foreground" title="{{ $job->exception }}">
                            {{ \Illuminate\Support\Str::limit($job->exception, 140) }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            {{ __('Failed :time', ['time' => \Illuminate\Support\Carbon::parse($job->failed_at)->diffForHumans()]) }}
                        </p>
                    </div>
                    <div class="flex shrink-0 gap-2">
                        <x-slate::button type="button" variant="outline" size="sm" wire:click="retry('{{ $job->uuid }}')">
                            {{ __('Retry') }}
                        </x-slate::button>
                        <x-electrik::confirm
                            :title="__('Discard this failed job?')"
                            :description="__('This cannot be undone.')"
                            :confirm-label="__('Discard')"
                            wire-click="forget('{{ $job->uuid }}')"
                        >
                            <x-slate::button type="button" variant="ghost" size="sm">
                                {{ __('Discard') }}
                            </x-slate::button>
                        </x-electrik::confirm>
                    </div>
                </div>
            @empty
                <x-slate::alert variant="success" :title="__('No failed jobs')" :description="__('The queue is healthy.')" />
            @endforelse
        </div>

        <div>
            {{ $jobs->links() }}
        </div>
    @endunless
</div>
