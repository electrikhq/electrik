<x-slot name="pagetitle">Edit Permission</x-slot>
<x-slot name="header">
    <x-app-ui::header background-color="light" icon="carbon-manage-protection">
        <x-slot name="heading">
            {{ __('Edit Permission') }}
        </x-slot>

        <x-slot name="breadcrumbs">
            {{ Breadcrumbs::render('permissions.edit',$permission) }}
        </x-slot>

        <x-slot name="actions">
            <x-app-ui::icon-button data-tooltip-target="t-c-back" tag="a" iconSize="md"
                icon="carbon-undo" href="{{ route('permissions.index') }}" />
        </x-slot>
    </x-app-ui::header>

</x-slot>

<x-app-ui::section>
    <x-app-ui::form wire:submit.prevent="submit">
        <x-app-ui::form.section heading="Basic information" description="Permissions basic information">
            <x-slot name="fields">
                <x-app-ui::input disabled="disabled" wire:model="permission.name" label="Permission Name" type="text"
                    id="permission.name" name="permission.name"
                    helper-text="permission name should be in the format 'permission-name'" />

                <x-app-ui::textarea wire:model="permission.description" label="Permission Description" type="text"
                    id="permission.description" name="permission.description" helper-text="Optional description" />
            </x-slot>
        </x-app-ui::form.section>

        <x-slot name="actions">
            <x-app-ui::button color="theme" type="submit" icon="carbon-folder-move-to">
                {{ __('Update') }}
            </x-app-ui::button>
        </x-slot>

    </x-app-ui::form>

</x-app-ui::section>
