<div class="space-y-8">
    <x-electrik::page-header
        title="{{ __('Email preview') }}"
        description="{{ __('Render package notification emails with sample data.') }}"
    />

    <x-slate::select wire:model.live="template" label="{{ __('Template') }}" class="max-w-sm">
        @foreach ($templates as $key => $label)
            <option value="{{ $key }}">{{ $label }}</option>
        @endforeach
    </x-slate::select>

    <div class="overflow-hidden rounded-xl border border-border/80 bg-card shadow-xs">
        <iframe
            title="{{ __('Email preview') }}"
            class="h-[32rem] w-full bg-white"
            srcdoc="{{ $html }}"
        ></iframe>
    </div>
</div>
