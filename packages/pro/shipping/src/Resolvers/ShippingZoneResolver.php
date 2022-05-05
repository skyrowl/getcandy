<?php

namespace GetCandy\Shipping\Resolvers;

use InvalidArgumentException;
use GetCandy\Models\Country;
use GetCandy\Shipping\DataTransferObjects\PostcodeLookup;
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
     * Set the postcode to use when resolving.
     *
     * @param string $postcode
     *
     * @return self
     */
    public function postcode(PostcodeLookup $postcodeLookup): self
    {
        $this->postcodes = collect([$postcodeLookup]);
        $this->types->push('postcodes');

        return $this;
    }

    /**
     * Set the postcodes to use for resolving.
     *
     * @param Collection $postcodes
     *
     * @return self
     */
    public function postcodes(Collection $postcodes): self
    {
        // Check we haven't got models that aren't countries.
        throw_if(
            $postcodes->filter(fn($postcode) => get_class($postcode) != PostcodeLookup::class)->count(),
            throw new InvalidArgumentException()
        );

        $this->postcodes = $postcodes;
        $this->types->push('postcodes');

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

        $query->where(function ($builder) {
            if ($this->countries->count()) {
                $builder->whereHas('countries', function ($query) {
                    $query->whereIn('country_id', $this->countries->pluck('id'));
                });
            }
            if ($this->postcodes->count()) {
                $builder->orWhereHas('postcodes', function ($query) {
                    foreach ($this->postcodes as $postcodeLookup) {
                        $query->orWhere(function ($qb) use ($postcodeLookup) {
                            $qb->wherePostcode($postcodeLookup->postcode)
                                ->whereCountryId($postcodeLookup->country->id);
                        });
                    }
                });
            }
        });

        if (!$this->countries->count() && !$this->postcodes->count()) {
            $this->types = collect(
                ['unrestricted']
            );
        }

        return $query->whereIn('type', $this->types)->get();
    }
}
