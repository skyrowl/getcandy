<?php

namespace GetCandy\Shipping\Http\Livewire\Pages;

use GetCandy\Models\Country;
use GetCandy\Models\CustomerGroup;
use GetCandy\Shipping\Models\ShippingZone;
use Illuminate\Support\Collection;
use Livewire\Component;

abstract class AbstractShippingZone extends Component
{
    /**
     * The shipping zone instance.
     *
     * @var \GetCandy\Shipping\Models\ShippingZone
     */
    public ShippingZone $shippingZone;

    /**
     * The selected countries for the zone.
     *
     * @var array
     */
    public array $selectedCountries = [];

    /**
     * Search term for filtering out countries.
     *
     * @var string
     */
    public ?string $countrySearchTerm = null;

    /**
     * The postcodes to associate to the zone.
     *
     * @var string
     */
    public string $postcodes = '';

    public function baseRules()
    {
        return [
            'postcodes' => 'nullable|string',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function mount()
    {
        $this->selectedCountries = $this->shippingZone->countries->pluck('id')->toArray();

        $this->postcodes = $this->shippingZone->postcodes->pluck('postcode')->join("\n");
    }

    /**
     * Save the ShippingZone.
     *
     * @return void
     */
    abstract public function save();

    /**
     * Save common details across new and existing zones.
     *
     * @return void
     */
    public function saveDetails()
    {
        if ($this->shippingZone->type != 'countries') {
            $this->shippingZone->countries()->detach();
            $this->selectedCountries = [];
        } else {
            $this->shippingZone->countries()->sync(
                $this->selectedCountries
            );
        }

        if ($this->shippingZone->type == 'postcodes') {
            $postcodes = explode("\n", $this->postcodes);

            $existing = $this->shippingZone->postcodes()->whereIn('postcode', $postcodes)->pluck('postcode');

            $postcodesToAdd = collect($postcodes)->reject(function ($postcode) use ($existing) {
                return $existing->contains($postcode);
            });

            $this->shippingZone->postcodes()->createMany(
                $postcodesToAdd->map(function ($postcode) {
                    return [
                        'postcode' => str_replace(' ', '', $postcode),
                    ];
                })
            );

            $this->postcodes = $this->shippingZone->postcodes()->pluck('postcode')->join("\n");
        } else {
            $this->shippingZone->postcodes()->delete();
        }
    }

    public function getCustomerGroupsProperty()
    {
        return CustomerGroup::get();
    }

    /**
     * Return a list of available countries.
     *
     * @return Collection
     */
    public function getCountriesProperty()
    {
        return Country::where('name', 'LIKE', "%{$this->countrySearchTerm}%")
            ->whereNotIn('id', $this->selectedCountries)->get();
    }

    /**
     * Return a list of countries related to the zone.
     *
     * @return Collection
     */
    public function getZoneCountriesProperty()
    {
        return Country::whereIn('id', $this->selectedCountries)->get();
    }
}
