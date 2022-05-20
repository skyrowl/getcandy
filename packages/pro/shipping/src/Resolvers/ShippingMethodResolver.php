<?php

namespace GetCandy\Shipping\Resolvers;

use GetCandy\Models\Cart;
use GetCandy\Models\Country;
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
     * The country to use when resolving.
     *
     * @var Country
     */
    protected ?Country $country = null;

    /**
     * The state to use when resolving.
     *
     * @var State
     */
    protected ?string $state = null;

    /**
     * The postcode to use when resolving.
     *
     * @var string
     */
    protected ?string $postcode = null;

    /**
     * Initialise the resolver.
     *
     * @param  Cart  $cart
     */
    public function __construct(Cart $cart = null)
    {
        $this->cart($cart);
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

        if ($shippingAddress = $this->cart->shippingAddress) {
            $this->country(
                $shippingAddress->country
            );
            $this->postcode(
                $shippingAddress->postcode
            );
            $this->state(
                $shippingAddress->state
            );
        }

        return $this;
    }

    /**
     * Set the value for country.
     *
     * @param  Country  $country
     * @return self
     */
    public function country(Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function state($state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Set the value for the postcode.
     *
     * @param  string  $postcode
     * @return self
     */
    public function postcode(string $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * Return the shipping methods applicable to the cart.
     *
     * @return Collection
     */
    public function get(): Collection
    {
        if (! $this->postcode || ! $this->country) {
            return collect();
        }

        $zones = Shipping::zones()->country(
            $this->country
        )->state(
            State::whereName($this->state)->first()
        )->postcode(
            new PostcodeLookup(
                postcode: $this->postcode,
                country: $this->country
            )
        )->get();

        $shippingMethods = collect();

        foreach ($zones as $zone) {
            $shippingMethods = $zone->shippingMethods;
            foreach ($shippingMethods as $shippingMethod) {
                $shippingMethods->push(
                    $shippingMethod
                );
            }
        }

        return $shippingMethods->filter()->unique(function ($method) {
            return $method->code;
        });
    }
}
