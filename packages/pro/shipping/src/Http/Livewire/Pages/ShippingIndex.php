<?php

namespace GetCandy\Shipping\Http\Livewire\Pages;

use Livewire\Component;

class ShippingIndex extends Component
{
    public function foo()
    {
        dd(1);
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
