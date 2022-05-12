<?php

namespace GetCandy\Shipping\Resolvers;

use GetCandy\Models\Cart;
use GetCandy\Shipping\DataTransferObjects\ShippingOptionLookup;
use GetCandy\Shipping\Events\ShippingOptionResolvedEvent;
use Illuminate\Support\Collection;

class ShippingOptionResolver
{
    /**
     * The cart to use when resolving.
     *
     * @var Cart
     */
    protected ?Cart $cart;

    /**
     * Initialise the resolver.
     *
     * @param  Cart  $cart
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
     * Return the shipping methods applicable to the cart.
     *
     * @return Collection
     */
    public function get(ShippingOptionLookup $shippingOptionLookup): Collection
    {
        $shippingOptions = collect();

        if (! $this->cart) {
            return collect();
        }

        foreach ($shippingOptionLookup->shippingMethods as $shippingMethod) {
            $shippingOptions->push((object) [
                'shippingMethod' => $shippingMethod,
                'option' => $shippingMethod->getShippingOption($this->cart),
            ]);
        }

        return $shippingOptions->filter()->unique(function ($option) {
            return $option->option->getIdentifier();
        })->each(function ($option) {
            ShippingOptionResolvedEvent::dispatch(
                $this->cart,
                $option->shippingMethod,
                $option->option
            );
        });
    }
}
