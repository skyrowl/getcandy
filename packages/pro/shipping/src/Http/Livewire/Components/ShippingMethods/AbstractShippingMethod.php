<?php

namespace GetCandy\Shipping\Http\Livewire\Components\ShippingMethods;

use GetCandy\Shipping\Models\ShippingMethod;
use GetCandy\Shipping\Models\ShippingZone;
use Livewire\Component;

abstract class AbstractShippingMethod extends Component
{
    /**
     * The related ShippingZone
     *
     * @var ShippingZone
     */
    public ShippingZone $shippingZone;

    /**
     * The ShippingMethod we're editing or creating.
     *
     * @var ShippingMethod
     */
    public ShippingMethod $shippingMethod;
}
