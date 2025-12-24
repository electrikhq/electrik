<?php

namespace App\Events\Billing;

use App\Models\Team;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentFailed
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Team $team,
        public array $paymentData
    ) {
        //
    }
}

