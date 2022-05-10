<?php

namespace GetCandy\Shipping;

use GetCandy\Facades\ShippingManifest;
use GetCandy\Models\Cart;
use GetCandy\Shipping\DataTransferObjects\ShippingOptionLookup;
use GetCandy\Shipping\Events\ShippingOptionResolvedEvent;
use GetCandy\Shipping\Facades\Shipping;

class ShippingModifier
{
    public function handle(Cart $cart)
    {
        $shippingMethods = Shipping::shippingMethods($cart)->get();

        $options = Shipping::shippingOptions($cart)->get(
            new ShippingOptionLookup(
                shippingMethods: $shippingMethods
            )
        );

        foreach ($options as $option) {
            ShippingOptionResolvedEvent::dispatch($cart, $option);
            ShippingManifest::addOption($option);
        }
    }
}
