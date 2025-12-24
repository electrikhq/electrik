<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasLocalDates;

class Address extends Model
{
    use HasFactory, HasLocalDates, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'addressable_type',
        'addressable_id',
        'type',
        'name',
        'email',
        'address_1',
        'address_2',
        'city',
        'state',
        'country',
        'pincode',
        'tax_ids',
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
     * Get the parent addressable model.
     *
     * @return MorphTo
     */
    public function addressable()
    {
        return $this->morphTo();
    }
}

