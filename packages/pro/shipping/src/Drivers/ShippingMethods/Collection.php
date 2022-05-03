<?php

namespace GetCandy\Shipping\Drivers\ShippingMethods;

use GetCandy\Shipping\Http\Livewire\Components\ShippingMethods\Collection as ShippingMethodsCollection;
use GetCandy\Shipping\Http\Livewire\Components\ShippingMethods\ShipBy as ShippingMethodsShipBy;
use GetCandy\Shipping\Interfaces\ShippingMethodInterface;

class Collection implements ShippingMethodInterface
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'Collection';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Allow customers to pick up their orders in store';
    }

    /**
     * Return the reference to the Livewire component.
     *
     * @return string
     */
    public function component(): string
    {
        return (new ShippingMethodsCollection())->getName();
    }
}
