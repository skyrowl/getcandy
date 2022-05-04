<?php

namespace GetCandy\Shipping\Actions;

use GetCandy\Base\Addressable;
use GetCandy\Shipping\Models\ShippingZone;
use GetCandy\Shipping\Models\ShippingZonePostcode;
use Illuminate\Support\Collection;

class GetZones
{
    public function execute(Addressable $address)
    {
        // Do we have a country?
        $countryZones = collect();

        if ($address->country_id) {
            $countryZones = $this->getCountryZones($address->country_id);
        }

        // Do we have a zone which matches the postcode?
        $postcodeZones = collect();

        if ($address->postcode) {
            $postcodeZones = $this->getPostcodeZones($address->postcode);
        }

        return $countryZones->merge($postcodeZones);
    }

    /**
     * Return shipping zones based on country
     *
     * @param string $countryId
     *
     * @return Collection
     */
    public function getCountryZones($countryId)
    {
        return ShippingZone::whereHas('countries', function ($query) use ($countryId) {
            $query->whereCountryId($countryId);
        })->get();
    }

    /**
     * Returns shipping zones that match via a postcode
     *
     * @param string $postcode
     *
     * @return Collection  description
     */
    public function getPostcodeZones($postcode)
    {
        $postcode = str_replace(' ', '', strtoupper($postcode));

        return ShippingZonePostcode::with([
            'shippingZone',
        ])->where(function ($query) use ($postcode) {
            $query->wherePostcode($postcode)
                ->orWhere(
                    'postcode',
                    '=',
                    rtrim(substr($postcode, 0, -3), 'a..zA..Z')
                )->orWhere(
                    'postcode',
                    '=',
                    rtrim($postcode, '0..9')
                )->orWhere(
                    'postcode',
                    '=',
                    substr($postcode, 0, 2)
                );
        })->get()->pluck('shippingZone');
    }
}
