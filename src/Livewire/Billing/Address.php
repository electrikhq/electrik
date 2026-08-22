<?php

namespace Electrik\Livewire\Billing;

use Electrik\Concerns\ResolvesTeamBilling;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Billing address')]
class Address extends Component
{
    use ResolvesTeamBilling;

    public string $name = '';

    public string $email = '';

    public string $line1 = '';

    public string $line2 = '';

    public string $city = '';

    public string $state = '';

    public string $postal_code = '';

    public string $country = '';

    public function mount(): void
    {
        $team = $this->currentTeamOrRedirect();

        if (! $team) {
            return;
        }

        abort_unless(
            auth()->user()->can('billing.view') || auth()->user()->isOwnerOfTeam($team),
            403
        );

        $this->name = $team->name;
        $this->email = $team->owner?->email ?? auth()->user()->email;

        if (! $team->hasStripeId()) {
            return;
        }

        try {
            $customer = $team->asStripeCustomer();
            $this->name = $customer->name ?: $this->name;
            $this->email = $customer->email ?: $this->email;
            $address = $customer->address;
            if ($address) {
                $this->line1 = $address->line1 ?? '';
                $this->line2 = $address->line2 ?? '';
                $this->city = $address->city ?? '';
                $this->state = $address->state ?? '';
                $this->postal_code = $address->postal_code ?? '';
                $this->country = $address->country ?? '';
            }
        } catch (\Throwable) {
            // Stripe customer may not exist yet
        }
    }

    public function save(): void
    {
        $team = $this->currentTeamOrRedirect();

        if (! $team) {
            return;
        }

        abort_unless(
            auth()->user()->can('billing.manage') || auth()->user()->isOwnerOfTeam($team),
            403
        );

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'line1' => ['required', 'string', 'max:255'],
            'line2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:32'],
            'country' => ['required', 'string', 'size:2'],
        ]);

        try {
            $team->createOrGetStripeCustomer();
            $team->updateStripeCustomer([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'address' => [
                    'line1' => $validated['line1'],
                    'line2' => $validated['line2'] ?: null,
                    'city' => $validated['city'],
                    'state' => $validated['state'],
                    'postal_code' => $validated['postal_code'],
                    'country' => strtoupper($validated['country']),
                ],
            ]);
            session()->flash('status', __('Billing address saved to Stripe.'));
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('electrik::livewire.billing.address');
    }
}
