<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\HasLocalDates;

class Team extends Model
{
    use HasFactory, Billable, HasApiTokens, HasLocalDates;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'owner_id',
        'slug',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tax_ids' => 'array',
        ];
    }

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($team) {
            // Create Stripe customer when team is created
            if (config('services.stripe.secret')) {
                $team->createOrGetStripeCustomer();
            }
        });

        static::updated(function ($team) {
            // Sync Stripe customer details when team is updated
            if ($team->hasStripeId()) {
                $team->syncStripeCustomerDetails();
            }
        });
    }

    /**
     * Get the owner of the team.
     *
     * @return BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the users that belong to the team.
     *
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'team_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the team's billing address.
     *
     * @return MorphOne
     */
    public function billingAddress()
    {
        return $this->morphOne(Address::class, 'addressable')
            ->where('type', 'billing');
    }

    /**
     * Get the team's mailing address.
     *
     * @return MorphOne
     */
    public function mailingAddress()
    {
        return $this->morphOne(Address::class, 'addressable')
            ->where('type', 'mailing');
    }

    /**
     * Get the Stripe customer name.
     *
     * @return string|null
     */
    public function stripeName()
    {
        if ($this->billingAddress && $this->billingAddress->name) {
            return $this->billingAddress->name;
        }

        return $this->name;
    }

    /**
     * Get the Stripe customer email.
     *
     * @return string|null
     */
    public function stripeEmail()
    {
        if ($this->billingAddress && $this->billingAddress->email) {
            return $this->billingAddress->email;
        }

        return $this->owner?->email;
    }

    /**
     * Get the Stripe customer address.
     *
     * @return array|null
     */
    public function stripeAddress()
    {
        if (!$this->billingAddress) {
            return null;
        }

        return [
            'line1' => $this->billingAddress->address_1,
            'line2' => $this->billingAddress->address_2,
            'city' => $this->billingAddress->city,
            'state' => $this->billingAddress->state,
            'country' => $this->billingAddress->country,
            'postal_code' => $this->billingAddress->pincode,
        ];
    }

    /**
     * Get the maximum number of team members allowed.
     *
     * @return int
     */
    public function allowedMaxTeamMembers()
    {
        // TODO: Make this dynamic based on subscription plan
        return 99;
    }
}

