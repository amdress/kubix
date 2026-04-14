<?php

namespace App\Models\Traits;

use App\Models\Address;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasAddresses
{
    /**
     * Get all addresses for this model.
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    /**
     * Get the primary address.
     */
    public function primaryAddress()
    {
        return $this->addresses()->where('is_primary', true)->first();
    }

    /**
     * Add a new address to this model.
     */
    public function addAddress(array $attributes): Address
    {
        // Si se está creando una dirección principal, quitar la principal actual
        if (isset($attributes['is_primary']) && $attributes['is_primary'] === true) {
            $this->addresses()->where('is_primary', true)->update(['is_primary' => false]);
        }

        return $this->addresses()->create($attributes);
    }

    /**
     * Check if the model has any addresses.
     */
    public function hasAddresses(): bool
    {
        return $this->addresses()->count() > 0;
    }

    /**
     * Get addresses by country.
     */
    public function addressesByCountry(string $country)
    {
        return $this->addresses()->where('country', strtoupper($country))->get();
    }
}