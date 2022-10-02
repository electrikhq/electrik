<div class="inline-flex items-center">
	@foreach ($row->getRoleNames()->take(3) as $role)
		<x-slate::badge>
			{{ $role }}
		</x-slate::badge>
	@endforeach
	@if($row->getRoleNames()->count() > 3) 
		<x-slate::badge>
			+{{ $row->getRoleNames()->count() }}
		</x-slate::badge>
	@endif
</div>