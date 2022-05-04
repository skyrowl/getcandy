<?php

namespace GetCandy\Shipping;

use GetCandy\Facades\ShippingManifest;
use GetCandy\Models\Cart;
use GetCandy\Shipping\Facades\Shipping;

class ShippingModifier
{
    public function handle(Cart $cart)
    {
        $options = Shipping::getShippingMethods($cart);

        foreach ($options as $option) {
            ShippingManifest::addOption($option);
        }
//         // Get the tax class
//         $taxClass = TaxClass::getDefault();
//
//         dd($cart);
//         ShippingManifest::addOption(
//             new ShippingOption(
//                 description: 'Basic Delivery',
//                 identifier: 'BASDEL',
//                 price: new Price(500, $cart->currency, 1),
//                 taxClass: $taxClass
//             )
//         );
//
//         ShippingManifest::addOption(
//             new ShippingOption(
//                 description: 'Express Delivery',
//                 identifier: 'EXPDEL',
//                 price: new Price(1000, $cart->currency, 1),
//                 taxClass: $taxClass
//             )
//         );
	}
}
