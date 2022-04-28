<?php

namespace GetCandy\Shipping\Drivers\ShippingMethods;

use GetCandy\Shipping\Http\Livewire\Components\ShippingMethods\FreeShipping as FreeShippingComponent;
use GetCandy\Shipping\Interfaces\ShippingMethodInterface;

class FreeShipping implements ShippingMethodInterface
{
    public function name(): string
    {
        return 'Free Shipping';
    }

    public function description(): string
    {
        return 'Offer free shipping for your customers';
    }

    /**
     * Return the reference to the Livewire component.
     *
     * @return string
     */
    public function component(): string
    {
        return (new FreeShippingComponent)->getName();
    }
}
