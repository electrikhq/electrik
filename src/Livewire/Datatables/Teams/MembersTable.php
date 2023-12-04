<?php

namespace App\Livewire\Datatables\Teams;

use App\Events\UserDeleted;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\User;
use Usernotnull\Toast\Concerns\WireToast;

class MembersTable extends DataTableComponent {

	use WireToast;

    protected $model = User::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
				->hideIf(true),
            Column::make("Name", "name")
                ->sortable(),
            Column::make("Email", "email")
                ->sortable(),
            Column::make("Created at", "created_at")
                ->sortable(),
			Column::make('Role')
				->label( function($row, Column $column) {
					return view('livewire.datatables.teams.members-table.columns.role')
							->with('row', $row)
							->with('column', $column);
				}),
			Column::make('Actions')
				->label( function($row, Column $column) {
					return view('livewire.datatables.teams.members-table.columns.action')
							->with('row', $row)
							->with('column', $column);
				}),
        ];
    }

	public function detachFromTeam($userId) {

		if(auth()->user()->id == $userId) {
			toast()->danger('You cannot remove yourself from the team')->push();
			return ;
		}

		$team = auth()->user()->currentTeam;
		$user = User::find($userId);
		$user->detachTeam($team);
		toast()->success('User removed from the system successfully')->push();
	}
}
