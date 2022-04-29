<?php

namespace GetCandy\Shipping\Http\Livewire\Pages;

use GetCandy\Shipping\Models\ShippingExclusionList;
use GetCandy\Shipping\Models\ShippingZone;
use Livewire\Component;

class ShippingExclusionListsIndex extends Component
{
    /**
     * Return the available shipping zones.
     *
     * @return void
     */
    public function getListsProperty()
    {
        return ShippingExclusionList::paginate();
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('shipping::exclusion-lists.index')
            ->layout('shipping::layout', [
                'title' => 'Shipping Exclusion Lists',
            ]);
    }
}
