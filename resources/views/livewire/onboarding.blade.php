<div class="space-y-6">
    <div class="flex items-center justify-center gap-2">
        @for ($i = 1; $i <= $totalSteps; $i++)
            <span @class([
                'size-2 rounded-full transition-colors',
                'bg-primary' => $step >= ($showPlanStep ? $i : ($i === 2 ? 3 : $i)),
                'bg-border' => ! ($step >= ($showPlanStep ? $i : ($i === 2 ? 3 : $i))),
            ])></span>
        @endfor
    </div>

    @if (session('status'))
        <x-slate::alert variant="success" :title="session('status')" />
    @endif

    @if ($step === 1)
        <x-slate::card class="border-border/80 shadow-xs">
            <x-slate::card-header>
                <x-slate::card-title>{{ __('Name your team') }}</x-slate::card-title>
                <x-slate::card-description>
                    {{ __('This is your workspace. You can rename it anytime in settings.') }}
                </x-slate::card-description>
            </x-slate::card-header>
            <x-slate::card-content>
                <x-slate::form wire:submit="saveTeam" class="space-y-4">
                    <x-slate::input wire:model="teamName" label="Team name" required autofocus />
                    <x-slate::button type="submit" size="lg" class="w-full">
                        {{ __('Continue') }}
                    </x-slate::button>
                </x-slate::form>
            </x-slate::card-content>
        </x-slate::card>
    @elseif ($step === 2 && $showPlanStep)
        <x-slate::card class="border-border/80 shadow-xs">
            <x-slate::card-header>
                <x-slate::card-title>{{ __('Choose a plan') }}</x-slate::card-title>
                <x-slate::card-description>
                    {{ __('Pick a plan now or skip and decide later.') }}
                </x-slate::card-description>
            </x-slate::card-header>
            <x-slate::card-content class="space-y-3">
                @error('plan')
                    <x-slate::alert variant="destructive" :title="$message" />
                @enderror

                @forelse ($plans as $plan)
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-border/80 p-4" wire:key="plan-{{ $plan->id }}">
                        <div>
                            <p class="font-medium">{{ $plan->name }}</p>
                            <p class="text-sm text-muted-foreground">
                                {{ $plan->formatted_price }}/{{ $plan->interval }}
                            </p>
                        </div>
                        <x-slate::button type="button" wire:click="subscribe({{ $plan->id }})" size="sm">
                            {{ $plan->isFree() ? __('Start free') : __('Subscribe') }}
                        </x-slate::button>
                    </div>
                @empty
                    <p class="text-sm text-muted-foreground">{{ __('No plans synced yet. Run electrik:stripe:sync after adding Stripe keys.') }}</p>
                @endforelse

                <x-slate::button type="button" variant="ghost" class="w-full" wire:click="skipPlan">
                    {{ __('Skip for now') }}
                </x-slate::button>
            </x-slate::card-content>
        </x-slate::card>
    @else
        <x-slate::card class="border-border/80 shadow-xs">
            <x-slate::card-header>
                <x-slate::card-title>{{ __('Invite teammates') }}</x-slate::card-title>
                <x-slate::card-description>
                    {{ __('Optional — you can always invite people from the Members page.') }}
                </x-slate::card-description>
            </x-slate::card-header>
            <x-slate::card-content>
                <x-slate::form wire:submit="sendInvite" class="space-y-4">
                    <x-slate::input
                        wire:model="inviteEmail"
                        type="email"
                        label="Email address"
                        placeholder="colleague@company.com"
                    />
                    @error('inviteEmail')
                        <p class="text-sm text-destructive">{{ $message }}</p>
                    @enderror
                    <x-slate::button type="submit" size="lg" class="w-full">
                        {{ __('Send invite & finish') }}
                    </x-slate::button>
                    <x-slate::button type="button" variant="ghost" class="w-full" wire:click="skipInvite">
                        {{ __('Skip for now') }}
                    </x-slate::button>
                </x-slate::form>
            </x-slate::card-content>
        </x-slate::card>
    @endif
</div>
