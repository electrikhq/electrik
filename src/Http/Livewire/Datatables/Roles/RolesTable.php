<?php

namespace App\Http\Livewire\Datatables\Roles;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Role;

class RolesTable extends DataTableComponent
{
    protected $model = Role::class;

    public function configure(): void {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
				->hideIf(true)
                ->sortable(),
			Column::make("name", "name")
				->sortable(),
            Column::make("display name", "display_name")
                ->sortable(),
			Column::make('Permissions')
				->label( function($row, Column $column) {
					return view('livewire.datatables.roles.roles-table.columns.permission')
							->with('row', $row)
							->with('column', $column);
				}),
				Column::make("Updated at", "updated_at")
                ->sortable(),
			Column::make('Actions')
				->label( function($row, Column $column) {
					return view('livewire.datatables.roles.roles-table.columns.action')
							->with('row', $row)
							->with('column', $column);
				}),
          
        ];
    }
}
