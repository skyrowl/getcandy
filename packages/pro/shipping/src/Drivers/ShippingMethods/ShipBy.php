<?php

namespace GetCandy\Shipping\Drivers\ShippingMethods;

use GetCandy\DataTypes\ShippingOption;
use GetCandy\Facades\Pricing;
use GetCandy\Models\Cart;
use GetCandy\Shipping\DataTransferObjects\ShippingOptionRequest;
use GetCandy\Shipping\Http\Livewire\Components\ShippingMethods\ShipBy as ShippingMethodsShipBy;
use GetCandy\Shipping\Interfaces\ShippingMethodInterface;
use GetCandy\Shipping\Models\ShippingMethod;

class ShipBy implements ShippingMethodInterface
{
    /**
     * The shipping method for context.
     *
     * @var ShippingMethod
     */
    public ShippingMethod $shippingMethod;

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'Ship By';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Offer a set price to ship per order total or per line total.';
    }

    /**
     * Return the reference to the Livewire component.
     *
     * @return string
     */
    public function component(): string
    {
        return (new ShippingMethodsShipBy())->getName();
    }

    public function resolve(ShippingOptionRequest $shippingOptionRequest): ShippingOption|null
    {
        $data = $shippingOptionRequest->shippingMethod->data;
        $cart = $shippingOptionRequest->cart;
        $shippingMethod = $shippingOptionRequest->shippingMethod;

        $chargeBy = $data->charge_by ?? null;

        if (! $chargeBy) {
            $chargeBy = 'cart_total';
        }

        $tier = $cart->subTotal->value;

        if ($chargeBy == 'weight') {
            // Do we even have weight on a cart???
            $tier = 100;
        }

        // Do we have a suitable tier price?
        $pricing = Pricing::for($shippingMethod)->qty($tier)->get();

        if (! $pricing->matched) {
            return null;
        }

        return new ShippingOption(
            name: $shippingMethod->name,
            description: $shippingMethod->description,
            identifier: $shippingMethod->getIdentifier(),
            price: $pricing->matched->price,
            taxClass: $shippingMethod->getTaxClass(),
            taxReference: $shippingMethod->getTaxReference(),
            option: $shippingMethod->getOption(),
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
}
