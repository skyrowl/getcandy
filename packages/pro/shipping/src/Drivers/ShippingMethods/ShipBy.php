<?php

namespace GetCandy\Shipping\Drivers\ShippingMethods;

use GetCandy\Shipping\Http\Livewire\Components\ShippingMethods\ShipBy as ShippingMethodsShipBy;
use GetCandy\Shipping\Interfaces\ShippingMethodInterface;

class ShipBy implements ShippingMethodInterface
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'Ship By';
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
        return (new ShippingMethodsShipBy())->getName();
    }
}
