<?php

namespace GetCandy\Shipping\Resolvers;

use GetCandy\Models\Cart;
use GetCandy\Models\State;
use GetCandy\Shipping\DataTransferObjects\PostcodeLookup;
use GetCandy\Shipping\Facades\Shipping;
use Illuminate\Support\Collection;

class ShippingMethodResolver
{
    /**
     * The cart to use when resolving.
     *
     * @var Cart
     */
    protected Cart $cart;

    /**
     * Initialise the resolver
     *
     * @param Cart $cart
     */
    public function __construct(Cart $cart = null)
    {
        $this->cart = $cart;
    }

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

    /**
     * Return the shipping methods applicable to the cart
     *
     * @return Collection
     */
    public function get(): Collection
    {
        $shippingAddress = $this->cart->shippingAddress;

        if (!$shippingAddress) {
            return collect();
        }

        $zones = Shipping::zones()->country(
            $shippingAddress->country
        )->state(
            State::whereName($shippingAddress->state)->first()
        )->postcode(
            new PostcodeLookup(
                postcode: $shippingAddress->postcode,
                country: $shippingAddress->country
            )
        )->get();

        $shippingOptions = collect();

        foreach ($zones as $zone) {
            $shippingMethods = $zone->shippingMethods;
            foreach ($shippingMethods as $shippingMethod) {
                $shippingOptions->push(
                    $shippingMethod->getShippingOption($this->cart)
                );
            }
        }

        return $shippingOptions->filter()->unique(function ($option) {
            return $option->getIdentifier();
        });
    }
}
