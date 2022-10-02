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
						'Up to 50 monthly orders',
						'Product review request emails',
						'Reviews management',
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
						'Up to 2000 monthly orders',
						'Product review request emails',
						'Reviews management',
						'Video & Photo Reviews'
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
						'Up to 4500 monthly orders',
						'Product review request emails',
						'Reviews management',
						'Video & Photo Reviews',
						'custom templates'
					],
				],
			],
		],
	]
];