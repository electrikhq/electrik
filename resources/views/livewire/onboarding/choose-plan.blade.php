<div class="flex flex-col h-screen w-full">

	<div class="">
		<div class="flex space-x-12 py-6 pl-6 border-b border-gray-200">
			<a class="text-lg font-bold" href="#" onlick="return false;" aria-current="page">{{ env("APP_NAME") }}</a>

			<a class="text-lg font-bold text-primary-600" href="#" onclick="return false;">1. Choose Plan</a>
			
			<a class="text-lg font-medium text-gray-600 " href="#" onclick="return false;">2. Create Account</a>

			<a class="text-lg font-medium text-gray-600" href="#" onclick="return false;">3. Confrim Details</a>
		</div>
	</div>
	
	<section class="bg-white dark:bg-gray-900">
		<div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
			<div class="mx-auto max-w-screen-md text-center mb-8 lg:mb-12">
				<h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">Supercharge your ecommerce marketing.</h2>
				<p class="mb-5 font- text-gray-500 sm:text-xl dark:text-gray-400">Whether you are a startup or a large ecommerce brand, we have a plan for you.</p>
			</div>
			<div class="space-y-8 md:grid md:grid-cols-3 sm:gap-6 lg:gap-10 md:space-y-0">
				@foreach (config('plans.billables.team.plans') as $plan )
					<!-- Pricing Card -->
					<div class="flex flex-col p-6 mx-auto max-w-lg text-center text-gray-900 bg-white rounded-lg border border-gray-100 shadow dark:border-gray-600 xl:p-8 dark:bg-gray-800 dark:text-white">
						<h3 class="mb-4 text-2xl font-semibold">{{ $plan['name'] }}</h3>
						<p class="font-medium text-gray-500 sm:text-lg dark:text-gray-400">{{ $plan['short_description'] }}</p>
						<div class="flex justify-center items-baseline my-8">
							<span class="mr-2 text-5xl font-extrabold">{{ $plan['prices']['us']['monthly']['price'] }}</span>
							<span class="text-gray-500 dark:text-gray-400">/{{ $plan['prices']['us']['monthly']['interval'] }}</span>
						</div>
						<!-- List -->
						<ul role="list" class="mb-8 space-y-4 text-left">
						@foreach ($plan['features'] as $feature )
							<li class="flex items-center space-x-3">
								<!-- Icon -->
								<svg class="flex-shrink-0 w-5 h-5 text-green-500 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
								<span>{!! $feature !!}</span>
							</li>
							@endforeach
						</ul>
						<x-slate::button :link="route('onboarding.register', ['plan' => $plan['slug']])" >Get started</x-slate::button>
					</div>
				@endforeach

			</div>
		</div>
	</section>
</div>
