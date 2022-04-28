<?php

namespace GetCandy\Shipping\Traits;

trait ExcludesProducts
{
    /**
     * Return the exclusions collection.
     *
     * @return void
     */
    public function getExclusionsProperty()
    {
        return $this->shippingMethod->shippingExclusions;
    }
}
