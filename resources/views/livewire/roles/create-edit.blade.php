<div>
	<x-slate::header full-width color="white">
		<x-slot name="title">
			<div class="flex items-center space-x-2">
				<x-slate::icon icon="carbon-events" color="black" />
				<x-slate::heading tag="h1" font-medium>
					Roles
				</x-slate::heading>
			</div>
		</x-slot>
		<x-slot name="meta">
			Manage your roles and permissions
		</x-slot>
		<x-slot name="actions">
			<x-slate::button icon="carbon-add" size="sm" :link="route('teams.roles.create')">Add Role</x-slate::button>
		</x-slot>
	</x-slate::header>


<div class="p-4">

		<x-slate::form wire:submit.prevent="submit" >

			<x-slate::input wire:model.defer="role.name" label="Role Name" type="text" id="role.name" name="role.name" :disabled="$role->exists" />
			@if(request()->routeIs('teams.roles.edit'))
				<x-slate::input wire:model.defer="role.display_name" label="Display Name" type="text" id="role.display_name" name="role.display_name" />
			@endif
			<x-slate::textarea wire:model.defer="role.description" label="Role Description" id="role.description" name="role.description" />

			<hr/>
			<div class="space-y-0">
				<x-slate::heading tag="h1" class="py-0 my-0">Permissions</x-slate::heading>
				<p class="text-gray-600 font-medium">These permissions will be assigned to users with this role</p>
			</div>

			@foreach($allPermissions as $permissions)

				<div class="space-y-0">
					<x-slate::heading class="mb-0" tag="h3" font-bold>{{ $permissions[0]['category_name'] }}</x-slate::heading>
					<p class="mt-0 mb-4">{{ $permissions[0]['category_description'] }}</p>
				</div>
				@foreach($permissions as $row)

					<x-slate::checkbox 
						wire:key="{{$row['id']}}-option" 
						wire:model.defer='selectedPermissionsIds' 
						name="selectedPermissionsIds.{{ $row['id'] }}" 
						label="{{ $row['display_name'] }}" 
						value="{{$row['id']}}" 
						helper-text="{{$row['description']}}" 
					/>

				@endforeach

				<hr/>
				
			@endforeach


			<x-slate::space />
			<x-slate::button>
				@if(request()->routeIs('teams.roles.edit'))
				{{ __('update role') }}
				@else
				{{ __('Add new role') }}
				@endif
			</x-slate::button>


		</x-slate::form>

</div>

</div>