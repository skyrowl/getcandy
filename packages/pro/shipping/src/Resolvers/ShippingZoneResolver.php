<?php

namespace GetCandy\Shipping\Resolvers;

use InvalidArgumentException;
use GetCandy\Models\Country;
use GetCandy\Shipping\Models\ShippingZone;
use Illuminate\Support\Collection;

class ShippingZoneResolver
{
    /**
     * The countries to use when resolving zones.
     *
     * @var Collection
     */
    protected Collection $countries;

    /**
     * The postcodes to use when resolving zones.
     *
     * @var Collection
     */
    protected Collection $postcodes;

    /**
     * The type of zones we want to query.
     *
     * @var Collection
     */
    protected Collection $types;

    /**
     * Initialise the resolver.
     */
    public function __construct()
    {
        $this->countries = collect();
        $this->postcodes = collect();
        $this->types = collect();
    }

    /**
     * Set the country
     *
     * @param Country $country
     *
     * @return self
     */
    public function country(Country $country = null): self
    {
        $this->countries = collect([$country]);
        $this->types->push('countries');

        return $this;
    }

    /**
     * Set the countries to use when resolving
     *
     * @param Collection $countries
     *
     * @return self
     */
    public function countries(Collection $countries): self
    {
        // Check we haven't got models that aren't countries.
        throw_if(
            $countries->filter(fn($country) => get_class($country) != Country::class)->count(),
            throw new InvalidArgumentException()
        );

        $this->countries = $countries;
        $this->types->push('countries');

        return $this;
    }

    /**
     * Return the shipping zones based on the criteria.
     *
     * @return Collection
     */
    public function get()
    {
        $query = ShippingZone::query();

        if ($this->countries->count()) {
            $query->whereHas('countries', function ($query) {
                $query->whereIn('country_id', $this->countries->pluck('id'));
            });
        }

        if ($this->countries->count()) {
            $this->type = 'countries';
        }

        if ($this->postcodes->count()) {
            $this->type = 'postcodes';
        }

        if ($this->type) {
            $query->whereType($this->type);
        }

        return $query->get();
    }
}
