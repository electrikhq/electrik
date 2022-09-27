<div>
	<p>
		<label for="name">Name</label>
		<input type="text" 
			wire:model="name"
			id="name"
			name="name"
		/>
	</p>
	<p>
		Hello {{ $name }}!
	</p>
</div>