<?php

namespace GetCandy\Shipping\Http\Livewire\Pages;

use GetCandy\Shipping\Models\ShippingZone;
use Livewire\Component;

class ShippingIndex extends Component
{
    /**
     * Return the available shipping zones.
     *
     * @return void
     */
    public function getShippingZonesProperty()
    {
        return ShippingZone::paginate();
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('shipping::index')
            ->layout('shipping::layout', [
                'title' => 'Shipping',
            ]);
    }
}
