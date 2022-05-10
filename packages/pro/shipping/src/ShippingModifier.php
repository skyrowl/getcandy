<?php

namespace GetCandy\Shipping;

use GetCandy\Facades\ShippingManifest;
use GetCandy\Models\Cart;
use GetCandy\Shipping\Facades\Shipping;
use GetCandy\Shipping\Events\ShippingOptionResolvedEvent;

class ShippingModifier
{
    public function handle(Cart $cart)
    {
        $options = Shipping::shippingMethods($cart)->get();

        foreach ($options as $option) {
            ShippingOptionResolvedEvent::dispatch($cart, $option);
            ShippingManifest::addOption($option);
        }
    }
}
