<?php

namespace GetCandy\Shipping\Interfaces;

interface ShippingMethodInterface
{
    /**
     * Return the name of the shipping method.
     *
     * @return string
     */
    public function name(): string;

    /**
     * Return the description of the shipping method.
     *
     * @return string
     */
    public function description(): string;

    /**
     * Return the reference to the Livewire component.
     *
     * @return string
     */
    public function component(): string;
}
