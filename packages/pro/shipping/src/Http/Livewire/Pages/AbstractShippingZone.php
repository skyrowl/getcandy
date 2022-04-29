<?php

namespace GetCandy\Shipping\Http\Livewire\Pages;

use GetCandy\Models\CustomerGroup;
use GetCandy\Shipping\Models\ShippingZone;
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
    public array $zoneCountries;

    /**
     * The placeholder country when selecting for zone.
     *
     * @var string
     */
    public ?string $countryPlaceholder = null;

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

    public bool $showShipByTotal = false;

    public bool $showFreeShipping = false;

    public bool $showFlatRateShipping = false;

    public function getCustomerGroupsProperty()
    {
        return CustomerGroup::get();
    }
}
