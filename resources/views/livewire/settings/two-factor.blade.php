<div>

		{{ $secretKey }}


		{!! $qrCode !!}

		<div class="border-2 border-gray-600 bg-gray-100 rounded-sm p-4 font-medium font-mono">

			@foreach($recoveryCodes as $code) 
				<span class="py-1 block font-mono">{{ $code }}</span>
			@endforeach

		</div>

</div>