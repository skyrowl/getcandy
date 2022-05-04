<?php

namespace GetCandy\Shipping\Drivers\ShippingMethods;

use GetCandy\DataTypes\ShippingOption;
use GetCandy\Models\Cart;
use GetCandy\Shipping\Http\Livewire\Components\ShippingMethods\FlatRate as ShippingMethodsFlatRate;
use GetCandy\Shipping\Interfaces\ShippingMethodInterface;
use GetCandy\Shipping\Models\ShippingMethod;

class FlatRate implements ShippingMethodInterface
{
    /**
     * The shipping method for context
     *
     * @var ShippingMethod
     */
    public ShippingMethod $shippingMethod;

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

    public function getShippingOption(Cart $cart): ShippingOption|null
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function on(ShippingMethod $shippingMethod): self
    {
        $this->shippingMethod = $shippingMethod;

        return $this;
    }
}
