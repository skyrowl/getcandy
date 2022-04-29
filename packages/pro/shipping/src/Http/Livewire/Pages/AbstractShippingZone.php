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
     * The placeholder country when selecting for zone.
     *
     * @var string
     */
    public ?string $countryPlaceholder = null;

    /**
     * Search term for filtering out countries
     *
     * @var string
     */
    public ?string $countrySearchTerm = null;

    /**
     * Add the selected country into the array.
     *
     * @param  int  $id
     * @return void
     */
    public function selectCountry($id)
    {
        $this->countryPlaceholder = null;
        $this->zoneCountries[] = $id;
    }

    public function baseRules()
    {
        return [
            'countryPlaceholder' => 'string|nullable',
        ];
    }

    /**
     * Save the ShippingZone.
     *
     * @return void
     */
    abstract public function save();

    public function getCustomerGroupsProperty()
    {
        return CustomerGroup::get();
    }

    /**
     * Return a list of available countries
     *
     * @return Collection
     */
    public function getCountriesProperty()
    {
        return Country::where('name', 'LIKE', "%{$this->countrySearchTerm}%")
            ->whereNotIn('id', $this->selectedCountries)->get();
    }

    public function getZoneCountriesProperty()
    {
        return Country::whereIn('id', $this->selectedCountries)->get();
    }
}
