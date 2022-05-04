<?php

namespace GetCandy\Shipping\Drivers\ShippingMethods;

use GetCandy\DataTypes\Price;
use GetCandy\DataTypes\ShippingOption;
use GetCandy\Models\Cart;
use GetCandy\Models\TaxClass;
use GetCandy\Shipping\Http\Livewire\Components\ShippingMethods\FreeShipping as FreeShippingComponent;
use GetCandy\Shipping\Interfaces\ShippingMethodInterface;
use GetCandy\Shipping\Models\ShippingMethod;

class FreeShipping implements ShippingMethodInterface
{
    /**
     * The shipping method for context
     *
     * @var ShippingMethod
     */
    public ShippingMethod $shippingMethod;

    /**
     * {@inheritDocs}
     */
    public function name(): string
    {
        return 'Free Shipping';
    }

    /**
     * {@inheritDocs}
     */
    public function description(): string
    {
        return 'Offer free shipping for your customers';
    }

    public function getShippingOption(Cart $cart): ShippingOption|null
    {
        $data = $this->shippingMethod->data;

        $subTotal = $cart->subTotal->value;

        if ($data->use_discount_amount) {
            $subTotal -= $cart->discountTotal->value;
        }

        $minSpend = (int) $data->minimum_spend->{$cart->currency->code} ?? null;

        if (!$minSpend || ($minSpend * 100) > $subTotal) {
            return null;
        }

        return new ShippingOption(
            name: $this->shippingMethod->name,
            description: $this->shippingMethod->description,
            identifier: $this->shippingMethod->code,
            price: new Price(0, $cart->currency, 1),
            taxClass: TaxClass::getDefault()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function on(ShippingMethod $shippingMethod): self
    {
        $this->shippingMethod = $shippingMethod;

        return $this;
    }

    /**
     * Return the reference to the Livewire component.
     *
     * @return string
     */
    public function component(): string
    {
        return (new FreeShippingComponent)->getName();
    }
}
