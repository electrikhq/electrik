<x-slate::dropdown-menu class="relative">
    <x-slate::dropdown-menu-trigger>
        <x-slate::button type="button" variant="ghost" size="sm" class="relative" aria-label="{{ __('Notifications') }}">
            <x-electrik::icon name="notification" class="size-4" />
            @if ($unreadCount > 0)
                <span class="absolute end-1 top-1 flex size-2 rounded-full bg-destructive" aria-hidden="true"></span>
            @endif
        </x-slate::button>
    </x-slate::dropdown-menu-trigger>

    <x-slate::dropdown-menu-content align="end" class="w-80 p-0">
        <div class="flex items-center justify-between border-b border-border px-3 py-2">
            <p class="text-sm font-medium">{{ __('Notifications') }}</p>
            @if ($unreadCount > 0)
                <button
                    type="button"
                    class="text-xs text-muted-foreground hover:text-foreground"
                    wire:click="markAllAsRead"
                >
                    {{ __('Mark all read') }}
                </button>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto p-1">
            @forelse ($notifications as $notification)
                @php($data = $notification->data)
                <x-slate::dropdown-menu-item
                    as="button"
                    type="button"
                    class="flex-col items-start gap-0.5 whitespace-normal {{ $notification->read_at ? 'opacity-70' : '' }}"
                    wire:click="markAsRead('{{ $notification->id }}')"
                >
                    <span class="font-medium">{{ $data['title'] ?? __('Notification') }}</span>
                    @if (! empty($data['body']))
                        <span class="text-xs text-muted-foreground">{{ $data['body'] }}</span>
                    @endif
                </x-slate::dropdown-menu-item>
            @empty
                <p class="px-3 py-6 text-center text-sm text-muted-foreground">{{ __('No notifications yet.') }}</p>
            @endforelse
        </div>
    </x-slate::dropdown-menu-content>
</x-slate::dropdown-menu>
