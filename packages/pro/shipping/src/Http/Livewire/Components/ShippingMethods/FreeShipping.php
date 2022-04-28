<?php

namespace GetCandy\Shipping\Http\Livewire\Components\ShippingMethods;

use Livewire\Component;

class FreeShipping extends AbstractShippingMethod
{
    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('shipping::shipping-methods.free-shipping')
        ->layout('adminhub::layouts.app', [
            'title' => 'United Kingdom',
        ]);
    }
}
