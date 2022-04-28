<?php

namespace GetCandy\Shipping\Drivers\ShippingMethods;

use GetCandy\Shipping\Interfaces\ShippingMethodInterface;

class FreeShipping implements ShippingMethodInterface
{
    /**
     * Return the reference to the Livewire component.
     *
     * @return string
     */
    public function component()
    {
        return '';
    }
}
