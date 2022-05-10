<?php

namespace GetCandy\Shipping\DataTransferObjects;

use Doctrine\Common\Cache\Psr6\InvalidArgument;
use GetCandy\Models\Country;
use GetCandy\Shipping\Models\ShippingMethod;
use Illuminate\Support\Collection;

class ShippingOptionLookup
{
    /**
     * Initialise the postcode lookup class.
     *
     * @param Country Country description
     * @param public string description
     */
    public function __construct(
        public Collection $shippingMethods
    ) {
        throw_if(
            $shippingMethods->filter(
                fn ($method) => get_class($method) != ShippingMethod::class
            )->count(),
            new InvalidArgument()
        );
    }
}
