<?php

return [

	/**
	 * 
	 * If you want to collect credit cards for free plans, set this value to true.
	 * If set false, it will not ask for credit card and billing details for the free plan defined in 'free_plan_id' config value in this file.
	 * 
	 */
	'cc_required_for_free_plan' => false,

	/**
	 * This is the default subscription name which will be saved in the db. 
	 * This is for internal use only and will not be shown to users.
	 * 
	 * Read more here: https://laravel.com/docs/9.x/billing#creating-subscriptions
	 */
	'default_subscription_name' => 'electrik',

	/**
	 * This is your free plan id defined in plans.php config file. 
	 * It is used in case the identufy a plan as a free plan in the system
	 */

	'free_plan_id' => 'prod_MOurX66bBOn5oD',

	/**
	 * This is your fallback plan id defined in plans.php config file. 
	 * It is used in case the selected plan is not available in the system.
	 * 
	 * This is the product id defined in Stripe dashboard.
	 */

	'fallback_plan_id' => 'prod_MOurX66bBOn5oD',
];