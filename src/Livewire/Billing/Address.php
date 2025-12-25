<?php

namespace App\Livewire\Billing;

use App\Actions\Billing\UpdateBillingAddress;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Address extends Component
{
    public $name = '';
    public $email = '';
    public $address_1 = '';
    public $address_2 = '';
    public $city = '';
    public $state = '';
    public $country = '';
    public $pincode = '';
    public $tax_ids = [];

    public function mount()
    {
        $team = Auth::user()->currentTeam;
        $address = $team->billingAddress;

        if ($address) {
            $this->name = $address->name ?? '';
            $this->email = $address->email ?? '';
            $this->address_1 = $address->address_1 ?? '';
            $this->address_2 = $address->address_2 ?? '';
            $this->city = $address->city ?? '';
            $this->state = $address->state ?? '';
            $this->country = $address->country ?? '';
            $this->pincode = $address->pincode ?? '';
            $this->tax_ids = $address->tax_ids ?? [];
        } else {
            // Set defaults from team owner
            $this->name = $team->name;
            $this->email = $team->owner->email ?? '';
        }
    }

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'address_1' => 'required|string|max:255',
        'address_2' => 'nullable|string|max:255',
        'city' => 'required|string|max:255',
        'state' => 'required|string|max:255',
        'country' => 'required|string|max:255',
        'pincode' => 'required|string|max:20',
        'tax_ids' => 'nullable|array',
    ];

    public function update()
    {
        $this->validate();

        $team = Auth::user()->currentTeam;

        try {
            $action = new UpdateBillingAddress();
            $action->execute($team, [
                'name' => $this->name,
                'email' => $this->email,
                'address_1' => $this->address_1,
                'address_2' => $this->address_2,
                'city' => $this->city,
                'state' => $this->state,
                'country' => $this->country,
                'pincode' => $this->pincode,
                'tax_ids' => $this->tax_ids,
            ]);

            session()->flash('message', 'Billing address updated successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update billing address: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.billing.address')
            ->layout('layouts.app', [
                'title' => 'Billing Address',
            ]);
    }
}

