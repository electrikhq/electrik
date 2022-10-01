<div>
	<x-slate::header full-width color="white">
		<x-slot name="title">
			<div class="flex items-center space-x-2">
				<x-slate::icon icon="carbon-events" color="black" />
				<x-slate::heading tag="h1" font-medium>
					Teams
				</x-slate::heading>
			</div>
		</x-slot>
		<x-slot name="meta">
			Manage your team and teammates
		</x-slot>
	</x-slate::header>



	<x-slate::section cols="1" class="max-w-2xl">
		<x-slate::heading tag="h2" font-medium>Team Name</x-slate::heading>
		<p>Change the name of your current team</p>
		<br />
		<x-slate::form wire:submit.prevent="update" full-width>
			<x-slate::input wire:model.defer="team.name" name="team.name" id="team.name" label="Team Name" helpText="Give your team a recognisable name." placeholder="Space Jammers" />
			<x-slot name="actions">
				<x-slate::button wire:click.prevent="update" class="secondary" wire:loading.attr="disabled" wire:loading.attr="outlined">
					<span wire:loading.remove wire:target="update">
						Update
					</span>
					<span wire:loading wire:target="update">
						Updating...
					</span>
				</x-slate::button>
			</x-slot>
		</x-slate::form>
	</x-slate::section>
	<hr />
	<x-slate::section cols="1" class="max-w-2xl">

		<x-slate::heading tag="h2" font-medium>Invite Team Members</x-slate::heading>

		<p>Details of your current team memeber's and their roles</p>
		<br />
		<x-slate::form wire:submit.prevent="invite" full-width>
			<x-slate::input wire:model.defer="invite.email" name="invite.email" id="invite.email" label="Email Address" helpText="Enter email address of the person you would like to add to your team." placeholder="teammate@yourcompany.com" />
			<x-slot name="actions">
				<x-slate::button wire:click.prevent="invite" class="secondary" wire:loading.attr="disabled" wire:loading.attr="outlined">
					<span wire:loading.remove wire:target="invite">
						Invite
					</span>
					<span wire:loading wire:target="invite">
						Updating...
					</span>
				</x-slate::button>
			</x-slot>
		</x-slate::form>
	</x-slate::section>
	<hr />
	<x-slate::section cols="1" class="maxxl">

			<x-slate::heading tag="h2" font-medium>Current Team Members</x-slate::heading>

				<p>Details of your current team memeber's and their roles</p>
<br/>
			<x-slate::heading class="my-2" tag="h4" uppercase>Team Memebers</x-slate::heading>
			<x-slate::table>

				<x-slot name="head">
					<x-slate::table-head-cell>
						Name
					</x-slate::table-head-cell>

					<x-slate::table-head-cell>
						Email
					</x-slate::table-head-cell>

					<x-slate::table-head-cell>
						Actions
					</x-slate::table-head-cell>
				</x-slot>

				@foreach($team->users as $user)

				<x-slate::table-row>
					<x-slate::table-cell>
						{{ $user->name }}
					</x-slate::table-cell>
					<x-slate::table-cell>
						{{ $user->email }}
					</x-slate::table-cell>
					<x-slate::table-cell class="text-center">
						@if(auth()->user()->isOwnerOfTeam($team))
						@if(auth()->user()->getKey() !== $user->getKey())
						<form style="display: inline-block;" action="{{route('teams.members.destroy', [$team, $user])}}" method="post">
							{!! csrf_field() !!}
							<input type="hidden" name="_method" value="DELETE" />
							<x-slate::icon icon="carbon-close-outline" />
						</form>
						@endif
						@endif

					</x-slate::table-cell>
				</x-slate::table-row>
				@endforeach
			</x-slate::table>
			<x-slate::space size="xs" />
			<x-slate::heading class="my-2" tag="h4" uppercase>Pending invitations</x-slate::heading>
			<x-slate::table>
				<x-slot name="head">
					<x-slate::table-head-cell>
						Name
					</x-slate::table-head-cell>

					<x-slate::table-head-cell>
						Email
					</x-slate::table-head-cell>

					<x-slate::table-head-cell>
						Actions
					</x-slate::table-head-cell>
				</x-slot>

				@foreach($team->invites as $invite)

				<x-slate::table-row>
					<x-slate::table-cell>
						{{ $invite->name }}
					</x-slate::table-cell>
					<x-slate::table-cell>
						{{ $invite->email }}
					</x-slate::table-cell>
					<x-slate::table-cell class="text-center">
						<x-slate::link href="{{route('teams.members.resend_invite', $invite)}}">
							<x-slate::icon icon="carbon-send-alt"></x-slate::icon>
						</x-slate::link>
					</x-slate::table-cell>
				</x-slate::table-row>
				@endforeach
			</x-slate::table>


	</x-slate::section>
</div>