<x-slot name="pagetitle">Permission Management</x-slot>
<x-slot name="header">

    <x-app-ui::header background-color="light" icon="carbon-manage-protection">
        <x-slot name="heading">
            {{ __('Permission Management') }}
        </x-slot>

        <x-slot name="breadcrumbs">
            {{ Breadcrumbs::render('permissions.index') }}
        </x-slot>
    </x-app-ui::header>

</x-slot>

<x-app-ui::section>
    <livewire:data-tables.access-management.permission-table />
</x-app-ui::section>
