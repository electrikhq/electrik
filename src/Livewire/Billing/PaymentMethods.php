<?php

namespace App\Livewire\Billing;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Exceptions\IncompletePayment;

class PaymentMethods extends Component
{
    public $paymentMethodId;

    public function mount()
    {
        if (!Auth::user()->currentTeam) {
            return redirect()->route('teams.index')
                ->with('error', 'Please select a team first.');
        }
    }

    public function setDefaultPaymentMethod($paymentMethodId)
    {
        $team = Auth::user()->currentTeam;
        
        try {
            $team->updateDefaultPaymentMethod($paymentMethodId);
            session()->flash('message', 'Default payment method updated successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update payment method: ' . $e->getMessage());
        }
    }

    public function deletePaymentMethod($paymentMethodId)
    {
        $team = Auth::user()->currentTeam;
        
        try {
            $paymentMethod = $team->findPaymentMethod($paymentMethodId);
            if ($paymentMethod) {
                $paymentMethod->delete();
                session()->flash('message', 'Payment method deleted successfully.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete payment method: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $team = Auth::user()->currentTeam;
        $paymentMethods = $team->paymentMethods();
        $defaultPaymentMethod = $team->defaultPaymentMethod();

        return view('livewire.billing.payment-methods', [
            'team' => $team,
            'paymentMethods' => $paymentMethods,
            'defaultPaymentMethod' => $defaultPaymentMethod,
        ])
            ->layout('layouts.app', [
                'title' => 'Payment Methods',
            ]);
    }
}

