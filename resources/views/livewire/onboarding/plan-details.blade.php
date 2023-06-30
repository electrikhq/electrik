<x-slate::heading tag="h1" class="">Your Trial Details.</x-slate::heading>
<div class="bg-white rounded-lg shadow-md p-4 mt-4">
	<x-slate::heading tag="h2">{{ $selectedPlan['name'] }}</x-slate::heading>
	<p class="">{{ $selectedPlan['short_description'] }} </p>
	
			
	<table class="border-t w-full mt-4">
		<tr class="text-lg">
			<td  class="py-2">
				Total after trial
			</td>
			
			<td  class=" text-right p-2">
			{{ $selectedPlan['prices']['us']['monthly']['text'] }}
			</td>
			
		</tr>
		<tr class=" text-xl" >
			<td class="py-2">
				<strong>Due today</strong>
			</td>
			<td style="text-align: right" class="p-2">
				<strong> 0</strong>
			</td>
		</tr>
	</table>
</div>
<p class="text-sm my-6">Your total may also include usage charges and taxes, if applicable.</p>
<p class="text-sm my-6">You will be charged in USD or INR based on your geographic location and final billing details.</p>
