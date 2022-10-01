<div class="inline-flex items-center">
	@foreach ($row->permissions()->limit(3)->get() as $permission)
		<x-slate::badge>
			{{ $permission->name }}
		</x-slate::badge>
	@endforeach
	@if($row->permissions()->count() > 3) 
		<x-slate::badge>
			+{{ $row->permissions()->count() }}
		</x-slate::badge>
	@endif
</div>