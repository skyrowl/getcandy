<?php

namespace GetCandy\Shipping\Managers;

use GetCandy\Shipping\Drivers\ShippingMethods\FlatRate;
use GetCandy\Shipping\Drivers\ShippingMethods\FreeShipping;
use GetCandy\Shipping\Interfaces\ShippingMethodManagerInterface;
use Illuminate\Support\Manager;

class ShippingManager extends Manager implements ShippingMethodManagerInterface
{
    public function createFreeShippingDriver()
    {
        return $this->buildProvider(FreeShipping::class);
    }

    public function createFlatRateDriver()
    {
        return $this->buildProvider(FlatRate::class);
    }

    public function getSupportedDrivers()
    {
        return collect(array_merge([
            'free-shipping' => $this->createDriver('free-shipping'),
            'flat-rate' => $this->createDriver('flat-rate'),
        ], $this->customCreators));
    }

    /**
     * Build a tax provider instance.
     *
     * @param  string  $provider
     * @return mixed
     */
    public function buildProvider($provider)
    {
        return $this->container->make($provider);
    }

    public function getDefaultDriver()
    {
        return 'free-shipping';
    }
}
