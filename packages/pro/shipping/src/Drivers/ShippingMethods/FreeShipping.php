<?php

namespace GetCandy\Shipping\Drivers\ShippingMethods;

use GetCandy\DataTypes\Price;
use GetCandy\DataTypes\ShippingOption;
use GetCandy\Models\TaxClass;
use GetCandy\Shipping\DataTransferObjects\ShippingOptionRequest;
use GetCandy\Shipping\Http\Livewire\Components\ShippingMethods\FreeShipping as FreeShippingComponent;
use GetCandy\Shipping\Interfaces\ShippingMethodInterface;
use GetCandy\Shipping\Models\ShippingMethod;

class FreeShipping implements ShippingMethodInterface
{
    /**
     * The shipping method for context.
     *
     * @var ShippingMethod
     */
    public ShippingMethod $shippingMethod;

    /**
     * {@inheritDoc}
     */
    public function name(): string
    {
        return 'Free Shipping';
    }

    /**
     * {@inheritDoc}
     */
    public function description(): string
    {
        return 'Offer free shipping for your customers';
    }

    public function resolve(ShippingOptionRequest $shippingOptionRequest): ShippingOption|null
    {
        $shippingMethod = $shippingOptionRequest->shippingMethod;
        $data = $shippingMethod->data;
        $cart = $shippingOptionRequest->cart;

        // Do we have any products in our exclusions list?
        // If so, we do not want to return this option regardless.
        $productIds = $cart->lines->load('purchasable')->pluck('purchasable.product_id');

        $hasExclusions = $shippingMethod->shippingExclusions()
            ->whereHas('exclusions', function ($query) use ($productIds) {
                $query->wherePurchasableType(Product::class)->whereIn('purchasable_id', $productIds);
            })->exists();

        if ($hasExclusions) {
            return null;
        }

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

        return new ShippingOption(
            name: $shippingMethod->name,
            description: $shippingMethod->description,
            identifier: $shippingMethod->code,
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
