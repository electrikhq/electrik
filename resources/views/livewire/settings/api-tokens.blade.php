<div class="mx-auto max-w-lg space-y-6">
    <x-electrik::page-header title="API tokens" description="Create tokens for programmatic access." />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif

    @if ($plainTextToken)
        <x-slate::alert variant="warning" title="Copy your token">
            <code class="mt-2 block break-all text-xs">{{ $plainTextToken }}</code>
        </x-slate::alert>
    @endif

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-content>
            <x-slate::form wire:submit="createToken" class="space-y-4">
                <x-slate::input wire:model="name" label="Token name" placeholder="CI deploy" required />
                <x-slate::button type="submit">Create token</x-slate::button>
            </x-slate::form>
        </x-slate::card-content>
    </x-slate::card>

    <div class="space-y-2">
        @forelse ($tokens as $token)
            <div class="flex items-center justify-between gap-3 rounded-xl border border-border/80 bg-card px-4 py-3" wire:key="token-{{ $token->id }}">
                <div>
                    <p class="font-medium">{{ $token->name }}</p>
                    <p class="text-xs text-muted-foreground">{{ $token->created_at?->diffForHumans() }}</p>
                </div>
                <x-slate::button type="button" variant="outline" size="sm" wire:click="deleteToken({{ $token->id }})">
                    Revoke
                </x-slate::button>
            </div>
        @empty
            <p class="text-sm text-muted-foreground">No tokens yet.</p>
        @endforelse
    </div>
</div>
