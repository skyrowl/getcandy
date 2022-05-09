# Shipping

[[toc]]

## Overview

On your checkout, if your customer has added an item that needs shipping, you're likely going to want to display some shipping options. Currently the best way to do this is to implement your own by adding a `ShippingModifier` and adding using that to determine what shipping options you want to make available and add them to the `ShippingManifest` class.

## Adding a Shipping Modifier

Create your own custom shipping provider:

```php
namespace App\Modifiers;

use GetCandy\Base\ShippingModifier;
use GetCandy\DataTypes\Price;
use GetCandy\DataTypes\ShippingOption;
use GetCandy\Facades\ShippingManifest;
use GetCandy\Models\Cart;
use GetCandy\Models\Currency;
use GetCandy\Models\TaxClass;

class CustomShippingModifier extends ShippingModifier
{
    public function handle(Cart $cart)
    {
        // Get the tax class
        $taxClass = TaxClass::first();

        ShippingManifest::addOption(
            new ShippingOption(
                description: 'Basic Delivery',
                identifier: 'BASDEL',
                price: new Price(500, $cart->currency, 1),
                taxClass: $taxClass
            )
        );
    }
}

```

In your service provider:

```php
public function boot(\GetCandy\Base\ShippingModifiers $shippingModifiers)
{
    $shippingModifiers->add(
        CustomShippingModifier::class
    );
}
```


## Adding a Shipping Driver

If you want to add your own shipping driver, you need to create a class that implements `GetCandy\Shipping\Interface\ShippingMethodInterface`

```php
<?php

namespace App\Shipping\Drivers;

use GetCandy\DataTypes\ShippingOption;
use GetCandy\Models\Cart;
use GetCandy\Shipping\Interfaces\ShippingMethodInterface;
use GetCandy\Shipping\Models\ShippingMethod;
use GetCandy\Shipping\DataTransferObjects\ShippingOptionRequest;

class OverWeightItems implements ShippingMethodInterface
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
        return 'Overweight Items';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Add Overweight items shipping option';
    }

    /**
     * Return the reference to the Livewire component.
     *
     * @return string
     */
    public function component(): string
    {
        return 'path.to.admin.settings.component';
    }

    public function resolve(ShippingOptionRequest $shippingOptionRequest): ShippingOption|null
    {
        // Logic to determine which shipping option should be returned.
        return null;
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
```

Once you've built out your driver, register it in your service provider.

```php
\GetCandy\Shipping\Facades\Shipping::extend('overweight-items', function ($app) {
    return $app->make(OverWeightItems::class);
});
```
