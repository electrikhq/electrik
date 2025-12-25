<?php

namespace App\Livewire\Billing;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Invoices extends Component
{
    public function downloadInvoice($invoiceId)
    {
        $team = Auth::user()->currentTeam;
        
        try {
            return $team->downloadInvoice($invoiceId, [
                'vendor' => config('app.name'),
                'product' => 'Subscription',
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to download invoice: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $team = Auth::user()->currentTeam;
        $invoices = $team->invoices();

        return view('livewire.billing.invoices', [
            'team' => $team,
            'invoices' => $invoices,
        ])
            ->layout('layouts.app', [
                'title' => 'Invoices',
            ]);
    }
}

