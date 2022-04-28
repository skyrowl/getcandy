<?php

namespace GetCandy\Shipping\Facades;

use GetCandy\Shipping\Interfaces\ShippingMethodManagerInterface;
use Illuminate\Support\Facades\Facade;

class Shipping extends Facade
{
    public static function getFacadeAccessor()
    {
        return ShippingMethodManagerInterface::class;
    }
}
