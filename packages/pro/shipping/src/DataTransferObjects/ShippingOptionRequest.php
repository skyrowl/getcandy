<?php

namespace GetCandy\Shipping\DataTransferObjects;

use GetCandy\Models\Cart;
use GetCandy\Shipping\Models\ShippingMethod;

class ShippingOptionRequest
{
    /**
     * Initialise the shipping option request class.
     *
     * @param  ShippingMethod  $shippingMethod
     * @param  Cart  $cart
     */
    public function __construct(
        public ShippingMethod $shippingMethod,
        public Cart $cart
    ) {
        //
    }
}
