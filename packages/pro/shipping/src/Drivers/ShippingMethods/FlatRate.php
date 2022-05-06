<?php

namespace GetCandy\Shipping\Drivers\ShippingMethods;

use GetCandy\DataTypes\ShippingOption;
use GetCandy\Facades\Pricing;
use GetCandy\Models\Cart;
use GetCandy\Shipping\DataTransferObjects\ShippingOptionRequest;
use GetCandy\Shipping\Http\Livewire\Components\ShippingMethods\FlatRate as ShippingMethodsFlatRate;
use GetCandy\Shipping\Interfaces\ShippingMethodInterface;
use GetCandy\Shipping\Models\ShippingMethod;

class FlatRate implements ShippingMethodInterface
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
        return 'Flat Rate Shipping';
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
        return (new ShippingMethodsFlatRate())->getName();
    }

    public function resolve(ShippingOptionRequest $shippingOptionRequest): ShippingOption|null
    {
        $data = $shippingOptionRequest->shippingMethod->data;
        $cart = $shippingOptionRequest->cart;
        $shippingMethod = $shippingOptionRequest->shippingMethod;

        $subTotal = $cart->subTotal->value;

        if ($data->use_discount_amount ?? false) {
            $subTotal -= $cart->discountTotal->value;
        }

        if (empty($data)) {
            $minSpend = 0;
        } else {
            if (is_array($data->minimum_spend)) {
                $minSpend = ($data->minimum_spend[$cart->currency->code] ?? null);
            } else {
                $minSpend = ($data->minimum_spend->{$cart->currency->code} ?? null);
            }
        }

        if (is_null($minSpend) || ($minSpend) > $subTotal) {
            return null;
        }

        $pricing = Pricing::for($shippingMethod)->qty($subTotal)->get();

        if (!$pricing->matched) {
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
