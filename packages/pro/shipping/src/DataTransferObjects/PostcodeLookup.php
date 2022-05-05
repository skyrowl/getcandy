<?php

namespace GetCandy\Shipping\DataTransferObjects;

use GetCandy\Models\Country;

class PostcodeLookup
{
    /**
     * Initialise the postcode lookup class
     *
     * @param Country Country description
     * @param public string description
     */
    public function __construct(
        public Country $country,
        public string $postcode
    ) {
        //
    }
}
