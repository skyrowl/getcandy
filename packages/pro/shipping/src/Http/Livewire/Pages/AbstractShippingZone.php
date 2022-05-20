<?php

namespace GetCandy\Shipping\Http\Livewire\Pages;

use GetCandy\Models\Country;
use GetCandy\Models\CustomerGroup;
use GetCandy\Models\State;
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
     * The selected states for the zone.
     *
     * @var array
     */
    public array $selectedStates = [];

    /**
     * The single country related to the zone.
     *
     * @var string
     */
    public ?string $country = null;

    /**
     * Search term for filtering out countries/states.
     *
     * @var string
     */
    public ?string $searchTerm = null;

    /**
     * The postcodes to associate to the zone.
     *
     * @var string
     */
    public ?string $postcodes = '';

    public function baseRules()
    {
        return [
            'postcodes' => 'nullable|string|required_if:shippingZone.type,postcodes',
            'country' => 'nullable|string|required_if:shippingZone.type,postcodes,shippingZone.type,states',
            'selectedCountries' => 'array|required_if:shippingZone.type,countries',
            'selectedStates' => 'array|required_if:shippingZone.type,states',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function mount()
    {
        if (
            in_array($this->shippingZone->type, ['postcodes', 'states'])
        ) {
            $this->country = $this->shippingZone->countries->pluck('id')->first();
        } else {
            $this->selectedCountries = $this->shippingZone->countries->pluck('id')->toArray();
        }

        $this->selectedStates = $this->shippingZone->states->pluck('id')->toArray();

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

        if ($this->shippingZone->type != 'states') {
            $this->shippingZone->states()->detach();
            $this->selectedStates = [];
        } else {
            $this->shippingZone->countries()->sync(
                [$this->country]
            );
            $this->shippingZone->states()->sync(
                $this->selectedStates
            );
        }

        if ($this->shippingZone->type == 'postcodes') {
            $this->shippingZone->countries()->sync(
                [$this->country]
            );

            $postcodes = collect(
                explode(
                    "\n",
                    str_replace(' ', '', $this->postcodes)
                )
            )->unique()->filter();

            $existing = $this->shippingZone->postcodes()->delete();

            $this->shippingZone->postcodes()->createMany(
                $postcodes->map(function ($postcode) {
                    return [
                        'postcode' => $postcode,
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
        return Country::where('name', 'LIKE', "%{$this->searchTerm}%")
            ->whereNotIn('id', $this->selectedCountries)->get();
    }

    public function getAllCountriesProperty()
    {
        return Country::get();
    }

    public function getStatesProperty()
    {
        return State::where('name', 'LIKE', "%{$this->searchTerm}%")
            ->whereNotIn('id', $this->selectedStates)
            ->whereCountryId($this->country)->get();
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

    /**
     * Return a list of states related to the zone.
     *
     * @return Collection
     */
    public function getZoneStatesProperty()
    {
        return State::whereIn('id', $this->selectedStates)->get();
    }
}
