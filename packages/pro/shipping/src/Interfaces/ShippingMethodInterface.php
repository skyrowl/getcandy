<?php

namespace GetCandy\Shipping\Interfaces;

use GetCandy\DataTypes\ShippingOption;
use GetCandy\Models\Cart;
use GetCandy\Shipping\Models\ShippingMethod;

interface ShippingMethodInterface
{
    /**
     * Return the name of the shipping method.
     *
     * @return string
     */
    public function name(): string;

    /**
     * Return the description of the shipping method.
     *
     * @return string
     */
    public function description(): string;

    /**
     * Return the reference to the Livewire component.
     *
     * @return string
     */
    public function component(): string;

    /**
     * Set the context for the driver
     *
     * @param ShippingMethod $shippingMethod
     *
     * @return self
     */
    public function on(ShippingMethod $shippingMethod): self;

    /**
     * Return the shipping option price
     *
     * @return ShippingOption
     */
    public function getShippingOption(Cart $cart): ShippingOption|null;
}
