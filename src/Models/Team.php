<?php
namespace App\Models;

use App\Traits\HasLocalDates;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Mpociot\Teamwork\TeamworkTeam;

use function Illuminate\Events\queueable;

class Team extends TeamworkTeam {

	use Billable;
	use HasLocalDates;
	use HasApiTokens;

	protected $casts = [
        'tax_ids' => 'array'
    ];

	protected $guarded = ['id'];

	protected static function booted() {
		static::created(queueable(function ($team) {        
			$stripeCustomer = $team->createOrGetStripeCustomer();
        }));
		
		static::updated(queueable(function ($customer) {
			if ($customer->hasStripeId()) {
				$customer->syncStripeCustomerDetails();
			}
		}));
	}

    // public function taxRates() {
    //     \Log::info('checking applicable tax raates');
    //     if($this->billingAddress()->first()->country == "IN") {
    //         \Log::info('india found');
    //         if($this->billingAddress()->first()->state == "UP") {
    //             \Log::info('delhi found');
	// 			\Log::info(env('CASHIER_TAX_RATE_SGST'));
	// 			\Log::info(env('CASHIER_TAX_RATE_CGST'));
    //             return [
    //                 env('CASHIER_TAX_RATE_SGST'),
    //                 env('CASHIER_TAX_RATE_CGST')
    //             ];
    //         } else {
    //             \Log::info('outside delhi found');
    //             return [
    //                 env('CASHIER_TAX_RATE_IGST'),
    //             ];
    //         }
    //     } else {
    //         if($this->billingAddress()->first()->country == "") {
    //             \Log::info('no country found');
    //             \Log::info('applying sgst' . env('CASHIER_TAX_RATE_SGST'));
    //             \Log::info('applying cgst' . env('CASHIER_TAX_RATE_CGST'));
    //             return [
    //                 env('CASHIER_TAX_RATE_SGST'),
    //                 env('CASHIER_TAX_RATE_CGST')
    //             ];
    //         } else {
    //             \Log::info('outside india found');
    //             return [
    //                 env('CASHIER_TAX_RATE_IGST'),
    //             ];
    //         }
    //     }
    // }
    
	/**
	 * Get the customer name that should be synced to Stripe.
	 *
	 * @return string|null
	 */
	public function stripeName() {

		$stripeName = null;
		if($this->billingAddress()->exists() && $this->billingAddress()->first()->name != null) {
			$stripeName = $this->billingAddress()->first()->name;
		} else {	
			$stripeName = $this->name;
		}

		// dd($stripeName);
		return $stripeName;
		
	}
    
	/**
	 * Get the customer's email that should be synced to Stripe.
	 *
	 * @return string|null
	 */
	public function stripeEmail() {

		$stripeEmail = null;

		if($this->billingAddress()->exists() && $this->billingAddress()->first()->email != null) {
			$stripeEmail = $this->billingAddress()->first()->email;
		} else {	
			$stripeEmail = $this->owner()->first()->email;
		}

		return $stripeEmail;
		
	}

	public function stripeAddress() {
		if($this->billingAddress()->exists()) {
			return [
			    'line1' => $this->billingAddress()->first()->address_1,
			    'line2' => $this->billingAddress()->first()->address_2,
			    'city' => $this->billingAddress()->first()->city,
			    'state' => $this->billingAddress()->first()->state,
			    'country' => $this->billingAddress()->first()->country,
			    'postal_code' => $this->billingAddress()->first()->pincode,
			];
		} else return null;
	}

	/** 
	 * @todo make this dynamic based on subscriptions
	 */
	function allowedMaxTeamMates() {
		return 99;
	}

	function logoUrl() {
		/**
		 * @todo fix this
		 */
		return env("APP_LOGO_URL", 'https://unsplash.it/100/100');
	}

	function billingAddress() {
		return $this->morphOne(Address::class, 'addressable')->where('addresses.type', 'billing');
	}

	function mailingAddress() {
		return $this->morphOne(Address::class, 'addressable')->where('addresses.type', 'mailing');
	}

	public function stores() {
		return $this->hasMany(Store::class);
	}

	public function integrations() {
		return $this->belongsToMany(Integration::class);
	}
	
	public function reminders() {
		return $this->hasMany(Reminder::class);
	}
	
	public function configuration() {
        return $this->morphMany(Configuration::class, 'configurable');
    }
	
	public function customers() {
        return $this->hasManyThrough(Customer::class, Store::class);
    }
	
	public function segments() {
        return $this->hasMany(Segment::class);
    }
	
	public function emailAddresses() {
        return $this->hasMany(EmailAddress::class);
    }
	
	public function templates() {
		return $this->hasMany(Team::class);
	}

}