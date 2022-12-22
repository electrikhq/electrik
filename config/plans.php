<?php

return [
	'billables' => [
		'team' => [
			'model' => Team::class,
			'trial_days' => 15,
			'plans' => [
				[
					'id' => 'prod_MOurX66bBOn5oD',
					'slug' => 'sprrw.core',
					'name' => 'Core',
					'short_description' => 'Everything required, for entrepreneurs just starting out',
					'trial_days' => 0,
					'prices' => [
						'in' => [
							'monthly' => [
								'id' => 'price_1Lg71sSEIFfRzRmSt0JVQp8C',
								'text' => 'FREE',
								'price' => 'FREE',
								'interval' => 'month',
							]
						],
						'us' => [
							'monthly' => [
								'id' => 'price_1Lg72PSEIFfRzRmSkBSCT4BO',
								'text' => 'FREE',
								'price' => 'FREE',
								'interval' => 'month',
							]
						],
					],
					'features' => [
						'Lorem ipsum dolor sit amet',
						'consectetur adipiscing elit',
						'Mauris lacinia mollis metus',
					],
				],
				[
					'id' => 'prod_MOusxp1eEFI6CM',
					'slug' => 'sprrw.growth',
					'name' => 'Growth',
					'short_description' => 'Businesses with traction who need a professional reviews platform',
					'trial_days' => 14,
					'prices' => [
						'in' => [
							'monthly' => [
								'id' => 'price_1Lg731SEIFfRzRmS1mfY1GSz',
								'text' => 'INR 1400 / month',
								'price' => 'INR 1400',
								'interval' => 'month',
							]
						],
						'us' => [
							'monthly' => [
								'id' => 'price_1Lg73ZSEIFfRzRmSivIS155R',
								'text' => '$14.99 / month',
								'price' => '$14.99',
								'interval' => 'month',
							]
						],
					],
					'features' => [
						'Aenean condimentum ex eget',
						'sem dictum gravida. Interdum',
						'et malesuada fam',
						'Maecenas hendrerit arcu'
					],
				],
				[
					'id' => 'prod_MOutUnhEHIT9YC',
					'slug' => 'sprrw.accelerate',
					'name' => 'Accelerate',
					'short_description' => 'For businesses who are looking to multiply their growth',
					'trial_days' => 14,
					'prices' => [
						'in' => [
							'monthly' => [
								'id' => 'price_1Lg742SEIFfRzRmSwcQkty41',
								'text' => 'INR 2400 / month',
								'price' => 'INR 2400',
								'interval' => 'month',
							]
						],
						'us' => [
							'monthly' => [
								'id' => 'price_1Lg74MSEIFfRzRmSE1G70fPL',
								'text' => '$21.99 / month',
								'price' => '$21.99',
								'interval' => 'month',
							]
						],
					],
					'features' => [
						'it amet dignissim eros',
						'tellus nulla imperdiet tortor',
						'Duis non congue quam',
						'nulla augue vestibulum quam',
						'iaculis turpis nulla vel sapien'
					],
				],
			],
		],
	]
];