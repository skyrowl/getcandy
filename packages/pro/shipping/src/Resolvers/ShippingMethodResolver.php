<?php

namespace GetCandy\Shipping\Resolvers;

use GetCandy\Models\Cart;

class ShippingMethodResolver
{
    /**
     * The cart to use when resolving.
     *
     * @var Cart
     */
    protected Cart $cart;

    /**
     * Set the cart.
     *
     * @param  Cart  $cart
     * @return self
     */
    public function cart(Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }
}
