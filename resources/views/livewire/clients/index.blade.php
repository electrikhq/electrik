<div class="space-y-8">
    <x-electrik::page-header
        title="{{ __('Clients') }}"
        :description="__('Accounts you deliver work for. Part of the sample Studio micro-app on :team.', ['team' => $team->name])"
    >
        <x-slot:actions>
            @if ($canManage && ! $editingId)
                <x-slate::button type="button" size="sm" x-data x-on:click="$refs.create?.scrollIntoView({ behavior: 'smooth' })">
                    {{ __('New client') }}
                </x-slate::button>
            @endif
        </x-slot:actions>
    </x-electrik::page-header>

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3">
        <x-slate::select wire:model.live="status" class="h-9 w-auto min-w-40">
            <option value="">{{ __('All statuses') }}</option>
            <option value="active">{{ __('Active') }}</option>
            <option value="paused">{{ __('Paused') }}</option>
            <option value="archived">{{ __('Archived') }}</option>
        </x-slate::select>
    </div>

    <div class="overflow-hidden rounded-xl border border-border/80">
        <table class="w-full text-sm">
            <thead class="border-b border-border/80 bg-muted/40 text-start text-xs font-medium uppercase tracking-wider text-muted-foreground">
                <tr>
                    <th class="px-4 py-3 text-start">{{ __('Client') }}</th>
                    <th class="px-4 py-3 text-start hidden sm:table-cell">{{ __('Company') }}</th>
                    <th class="px-4 py-3 text-start">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-start">{{ __('Projects') }}</th>
                    <th class="px-4 py-3 text-end"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clients as $client)
                    <tr class="border-b border-border/60 last:border-0" wire:key="client-{{ $client->id }}">
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ $client->name }}</p>
                            @if ($client->email)
                                <p class="text-xs text-muted-foreground">{{ $client->email }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell text-muted-foreground">{{ $client->company ?: '—' }}</td>
                        <td class="px-4 py-3">
                            <x-slate::badge variant="{{ $client->status === 'active' ? 'default' : 'secondary' }}">
                                {{ $client->statusLabel() }}
                            </x-slate::badge>
                        </td>
                        <td class="px-4 py-3">{{ number_format($client->projects_count) }}</td>
                        <td class="px-4 py-3 text-end">
                            @if ($canManage)
                                <div class="inline-flex flex-wrap justify-end gap-2">
                                    <x-slate::button type="button" size="sm" variant="outline" wire:click="edit({{ $client->id }})">
                                        {{ __('Edit') }}
                                    </x-slate::button>
                                    <x-electrik::confirm
                                        :title="__('Delete client?')"
                                        :description="__('Projects stay, but lose this client link.')"
                                        :confirm-label="__('Delete')"
                                        wire-click="delete({{ $client->id }})"
                                    >
                                        <x-slate::button type="button" size="sm" variant="destructive">{{ __('Delete') }}</x-slate::button>
                                    </x-electrik::confirm>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-muted-foreground">
                            {{ __('No clients yet. Add one to attach projects.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($clients->hasPages())
        <div>{{ $clients->links() }}</div>
    @endif

    @if ($canManage)
        <x-slate::card class="max-w-xl border-border/80 shadow-xs" x-ref="create">
            <x-slate::card-header>
                <x-slate::card-title>{{ $editingId ? __('Edit client') : __('New client') }}</x-slate::card-title>
                <x-slate::card-description>{{ __('Who you bill and deliver for.') }}</x-slate::card-description>
            </x-slate::card-header>
            <x-slate::card-content>
                <x-slate::form wire:submit="{{ $editingId ? 'update' : 'create' }}" class="space-y-4">
                    <x-slate::input wire:model="name" label="{{ __('Name') }}" required />
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-slate::input wire:model="email" type="email" label="{{ __('Email') }}" />
                        <x-slate::input wire:model="company" label="{{ __('Company') }}" />
                    </div>
                    <x-slate::select wire:model="clientStatus" label="{{ __('Status') }}">
                        <option value="active">{{ __('Active') }}</option>
                        <option value="paused">{{ __('Paused') }}</option>
                        <option value="archived">{{ __('Archived') }}</option>
                    </x-slate::select>
                    <x-slate::textarea wire:model="notes" label="{{ __('Notes') }}" rows="3" />
                    <div class="flex flex-wrap gap-2">
                        <x-slate::button type="submit">{{ $editingId ? __('Save changes') : __('Create client') }}</x-slate::button>
                        @if ($editingId)
                            <x-slate::button type="button" variant="ghost" wire:click="cancelEdit">{{ __('Cancel') }}</x-slate::button>
                        @endif
                    </div>
                </x-slate::form>
            </x-slate::card-content>
        </x-slate::card>
    @endif
</div>
