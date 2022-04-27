<?php

namespace GetCandy\Shipping\Http\Livewire\Pages;

use GetCandy\Models\CustomerGroup;
use GetCandy\Models\Product;
use Livewire\Component;

class ShippingZone extends Component
{
    public bool $showShipByTotal = false;

    public function getCustomerGroupsProperty()
    {
        return CustomerGroup::get();
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $products = Product::inRandomOrder()->take(10)->get();

        return view('shipping::shipping-zone', [
            'products' => $products,
        ])->layout('shipping::layout', [
            'title' => 'United Kingdom',
        ]);
    }
}
