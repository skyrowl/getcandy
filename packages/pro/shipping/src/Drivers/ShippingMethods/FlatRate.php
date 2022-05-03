<?php

namespace GetCandy\Shipping\Drivers\ShippingMethods;

use GetCandy\Shipping\Http\Livewire\Components\ShippingMethods\FlatRate as ShippingMethodsFlatRate;
use GetCandy\Shipping\Interfaces\ShippingMethodInterface;

class FlatRate implements ShippingMethodInterface
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'Flat Rate Shipping';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Offer a set price to ship per order total or per line total.';
    }

    /**
     * Return the reference to the Livewire component.
     *
     * @return string
     */
    public function component(): string
    {
        return (new ShippingMethodsFlatRate())->getName();
    }
}
