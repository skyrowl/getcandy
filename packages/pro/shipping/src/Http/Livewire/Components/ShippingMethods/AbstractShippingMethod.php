<?php

namespace GetCandy\Shipping\Http\Livewire\Pages;

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
}
