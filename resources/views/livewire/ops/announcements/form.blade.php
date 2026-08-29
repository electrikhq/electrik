<div class="mx-auto max-w-2xl space-y-6">
    <x-electrik::page-header
        :title="$editing ? __('Edit announcement') : __('New announcement')"
        description="{{ __('Broadcast an update to users as an in-app notification.') }}"
    />

    @if (session('error'))
        <x-slate::alert variant="destructive" :title="session('error')" />
    @endif

    <x-slate::card class="border-border/80 shadow-xs">
        <x-slate::card-content>
            <x-slate::form wire:submit="save" class="space-y-4">
                <x-slate::input wire:model="title" label="{{ __('Title') }}" required />

                <x-slate::textarea wire:model="body" label="{{ __('Body') }}" rows="4" required />

                <x-slate::select wire:model.live="audience" label="{{ __('Audience') }}">
                    <option value="all">{{ __('Everyone') }}</option>
                    <option value="operators">{{ __('Operators') }}</option>
                    <option value="plan">{{ __('Users on a plan') }}</option>
                </x-slate::select>

                @if ($audience === 'plan')
                    <x-slate::select wire:model="plan_price_id" label="{{ __('Plan') }}" required>
                        <option value="">{{ __('Select a plan…') }}</option>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->stripe_price_id }}">{{ $plan->name }} ({{ $plan->formatted_price }})</option>
                        @endforeach
                    </x-slate::select>
                @endif

                <div class="grid gap-4 sm:grid-cols-2">
                    <x-slate::input
                        type="datetime-local"
                        wire:model="starts_at"
                        label="{{ __('Starts at') }}"
                        description="{{ __('Optional. Leave blank to start immediately.') }}"
                    />
                    <x-slate::input
                        type="datetime-local"
                        wire:model="ends_at"
                        label="{{ __('Ends at') }}"
                        description="{{ __('Optional. Leave blank to run indefinitely.') }}"
                    />
                </div>

                <x-slate::button type="submit" size="lg">{{ __('Save') }}</x-slate::button>
            </x-slate::form>
        </x-slate::card-content>
    </x-slate::card>
</div>
