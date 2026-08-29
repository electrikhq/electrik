<div class="mx-auto max-w-lg space-y-6">
    <x-electrik::page-header
        title="{{ __('API tokens') }}"
        description="{{ __('Create personal or team-scoped tokens for programmatic access. Copy new secrets immediately — they are only shown once.') }}"
    />

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif

    @if ($plainTextToken)
        <div class="space-y-3 rounded-xl border border-amber-500/40 bg-amber-500/5 p-4 shadow-xs">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium">{{ __('Copy your token') }}</p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        {{ __('This is the only time the secret will be shown. Store it somewhere safe.') }}
                        @if ($createdTokenName)
                            · {{ $createdTokenName }}
                        @endif
                    </p>
                </div>
                <x-slate::button type="button" variant="ghost" size="sm" wire:click="dismissPlainToken">
                    {{ __('Dismiss') }}
                </x-slate::button>
            </div>
            <code class="block max-w-full overflow-x-auto whitespace-pre-wrap break-all font-mono text-xs leading-relaxed text-foreground">{{ $plainTextToken }}</code>
        </div>
    @endif

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-header>
            <x-slate::card-title>{{ __('Create token') }}</x-slate::card-title>
            <x-slate::card-description>
                {{ __('Personal tokens act as you. Team tokens are limited to the current team (:team).', ['team' => $currentTeam?->name ?? __('none')]) }}
            </x-slate::card-description>
        </x-slate::card-header>
        <x-slate::card-content>
            <x-slate::form wire:submit="createToken" class="space-y-4">
                <x-slate::input wire:model="name" label="{{ __('Token name') }}" placeholder="CI deploy" required />
                <x-slate::select wire:model="scope" label="{{ __('Scope') }}" required>
                    <option value="personal">{{ __('Personal (all your teams)') }}</option>
                    <option value="team" @disabled(! $currentTeam)>{{ __('Team only') }}@if ($currentTeam) — {{ $currentTeam->name }}@endif</option>
                </x-slate::select>
                <x-slate::select wire:model="expiresIn" label="{{ __('Expires') }}" required>
                    @foreach ($expirationOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-slate::select>
                <fieldset class="space-y-2">
                    <legend class="text-sm font-medium">{{ __('Abilities') }}</legend>
                    <p class="text-xs text-muted-foreground">{{ __('Choose Full access or specific abilities.') }}</p>
                    <div class="space-y-2 rounded-lg border border-border/80 p-3">
                        @foreach ($abilityCatalog as $ability => $label)
                            <label class="flex items-center gap-2 text-sm">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectedAbilities"
                                    value="{{ $ability }}"
                                    class="size-4 rounded border-border"
                                />
                                <span>{{ __($label) }} <span class="text-xs text-muted-foreground font-mono">({{ $ability }})</span></span>
                            </label>
                        @endforeach
                    </div>
                    @error('selectedAbilities')
                        <p class="text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </fieldset>
                <x-slate::button type="submit">{{ __('Create token') }}</x-slate::button>
            </x-slate::form>
        </x-slate::card-content>
    </x-slate::card>

    <div class="space-y-2">
        @forelse ($tokens as $token)
            @php
                $expired = $token->expires_at && $token->expires_at->isPast();
                $teamName = $token->team_id ? ($teams[$token->team_id]->name ?? __('Unknown team')) : null;
            @endphp
            <div class="rounded-xl border border-border/80 bg-card px-4 py-3" wire:key="token-{{ $token->id }}">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0 space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-medium">{{ $token->name }}</p>
                            @if ($token->team_id)
                                <x-slate::badge variant="secondary">{{ __('Team') }}: {{ $teamName }}</x-slate::badge>
                            @else
                                <x-slate::badge variant="secondary">{{ __('Personal') }}</x-slate::badge>
                            @endif
                            @if ($expired)
                                <x-slate::badge variant="destructive">{{ __('Expired') }}</x-slate::badge>
                            @elseif (! $token->expires_at)
                                <x-slate::badge variant="secondary">{{ __('No expiry') }}</x-slate::badge>
                            @endif
                        </div>
                        <dl class="space-y-0.5 text-xs text-muted-foreground">
                            <div>{{ __('Created :date', ['date' => $token->created_at?->toDayDateTimeString()]) }}</div>
                            <div>
                                @if ($token->last_used_at)
                                    {{ __('Last used :date', ['date' => $token->last_used_at->diffForHumans()]) }}
                                @else
                                    {{ __('Never used') }}
                                @endif
                            </div>
                            <div>
                                @if ($token->expires_at)
                                    @if ($expired)
                                        {{ __('Expired :date', ['date' => $token->expires_at->toDayDateTimeString()]) }}
                                    @else
                                        {{ __('Expires :date', ['date' => $token->expires_at->toDayDateTimeString()]) }}
                                    @endif
                                @else
                                    {{ __('Does not expire') }}
                                @endif
                            </div>
                        </dl>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        <x-electrik::confirm
                            :title="__('Reissue this token?')"
                            :description="__('The current secret stops working immediately. A new secret is shown once.')"
                            :confirm-label="__('Reissue')"
                            confirm-variant="default"
                            wire-click="reissueToken({{ $token->id }})"
                        >
                            <x-slate::button type="button" variant="outline" size="sm">
                                {{ __('Reissue') }}
                            </x-slate::button>
                        </x-electrik::confirm>
                        <x-electrik::confirm
                            :title="__('Revoke this token?')"
                            :description="__('Apps using this token will lose access immediately.')"
                            :confirm-label="__('Revoke')"
                            wire-click="deleteToken({{ $token->id }})"
                        >
                            <x-slate::button type="button" variant="outline" size="sm">
                                {{ __('Revoke') }}
                            </x-slate::button>
                        </x-electrik::confirm>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-sm text-muted-foreground">{{ __('No tokens yet.') }}</p>
        @endforelse
    </div>
</div>
