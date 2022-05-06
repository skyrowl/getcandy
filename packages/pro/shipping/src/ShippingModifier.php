<?php

namespace GetCandy\Shipping;

use GetCandy\Facades\ShippingManifest;
use GetCandy\Models\Cart;
use GetCandy\Shipping\Facades\Shipping;

class ShippingModifier
{
    public function handle(Cart $cart)
    {
        $options = Shipping::shippingMethods($cart)->get();

        foreach ($options as $option) {
            ShippingManifest::addOption($option);
        }
    }
}
