<?php

namespace Electrik\Http\Livewire\Datatables\Teams;

use Electrik\Models\TeamInvite;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Electrik\Models\User;
use Mpociot\Teamwork\Events\UserInvitedToTeam;
use Usernotnull\Toast\Concerns\WireToast;

class InvitedMembersTable extends DataTableComponent {
	
	use WireToast;

    protected $model = TeamInvite::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
		return [
            Column::make("Id", "id")
			->hideIf(true),
            Column::make("Email", "email")
                ->sortable(),
			Column::make('Role', 'role.name')
				->sortable(),
			Column::make('Actions')
				->label(function($row, Column $column) {
					return view('electrik::livewire.datatables.teams.invited-members-table.columns.action', [
						'row' => $row,
						'column' => $column,
					]);
				})->html(),
        ];
    }


    public function query(): Builder {
		return auth()->user()->currentTeam->invites()->getQuery();
    }

	public function deleteInvite($id) {

		$invite = TeamInvite::findOrFail($id);
		$invite->delete();
        toast()->success('Team invitation deleted successfully')->push();

	}

	public function resendInvite($id) {
		
		$invite = TeamInvite::findOrFail($id);
		event(new UserInvitedToTeam($invite));
        toast()->success('Team invitation resent successfully')->push();

	}
}
