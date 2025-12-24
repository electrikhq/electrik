<?php

namespace App\Actions\Billing;

use App\Models\Address;
use App\Models\Team;

class UpdateBillingAddress
{
    /**
     * Execute the action.
     *
     * @param  Team  $team
     * @param  array  $addressData
     * @return Address
     */
    public function execute(Team $team, array $addressData): Address
    {
        $address = $team->billingAddress()->firstOrNew();
        
        $address->fill([
            'type' => 'billing',
            'name' => $addressData['name'] ?? null,
            'email' => $addressData['email'] ?? null,
            'address_1' => $addressData['address_1'] ?? null,
            'address_2' => $addressData['address_2'] ?? null,
            'city' => $addressData['city'] ?? null,
            'state' => $addressData['state'] ?? null,
            'country' => $addressData['country'] ?? null,
            'pincode' => $addressData['pincode'] ?? null,
            'tax_ids' => $addressData['tax_ids'] ?? null,
        ]);

        if (!$address->exists) {
            $team->billingAddress()->save($address);
        } else {
            $address->save();
        }

        // Sync Stripe customer details
        if ($team->hasStripeId()) {
            $team->syncStripeCustomerDetails();
        }

        return $address;
    }
}

